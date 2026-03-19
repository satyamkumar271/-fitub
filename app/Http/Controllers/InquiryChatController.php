<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\InquiryBlock;
use App\Models\InquiryMessage;
use App\Models\InquiryReadState;
use App\Models\InquiryReport;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class InquiryChatController extends Controller
{
    public function myInquiries()
    {
        $user = auth()->user();
        $inquiries = Inquiry::with('recipient')
            ->where('user_id', $user->id)
            ->whereDoesntHave('blocks', function ($q) use ($user) {
                $q->where('active', true)
                  ->where(function ($sub) use ($user) {
                      $sub->where('blocker_id', $user->id)
                          ->orWhere('blocked_user_id', $user->id);
                  });
            })
            ->whereDoesntHave('reports', function ($q) use ($user) {
                $q->whereIn('status', ['open', 'under_review'])
                  ->where(function ($sub) use ($user) {
                      $sub->where('reporter_id', $user->id)
                          ->orWhere('reported_user_id', $user->id);
                  });
            })
            ->latest()
            ->paginate(20);

        return view('chat.my-inquiries', compact('inquiries'));
    }

    public function show(Inquiry $inquiry)
    {
        $user = auth()->user();
        $this->ensureParticipantAccess($inquiry, $user);

        InquiryReadState::updateOrCreate(
            [
                'inquiry_id' => $inquiry->id,
                'user_id' => $user->id,
            ],
            [
                'last_read_at' => now(),
            ]
        );

        $otherUser = $this->getOtherParticipant($inquiry, $user);
        $canSend = $this->canSendMessages($inquiry, $user, $otherUser);
        $isBlocked = $this->isBlocked($inquiry->id, $user->id, $otherUser?->id);

        // If the inquiry was created with a message (stored on inquiries table),
        // but no chat messages exist yet, seed the first message so chat history is visible.
        if (!empty($inquiry->user_id) && !empty($inquiry->message)) {
            $hasAny = InquiryMessage::where('inquiry_id', $inquiry->id)->exists();
            if (!$hasAny) {
                InquiryMessage::create([
                    'inquiry_id' => $inquiry->id,
                    'sender_id' => $inquiry->user_id,
                    'message' => $inquiry->message,
                ]);
            }
        }

        $messages = InquiryMessage::with('sender')
            ->where('inquiry_id', $inquiry->id)
            ->oldest()
            ->get();

        return view('chat.show', [
            'inquiry' => $inquiry,
            'messages' => $messages,
            'otherUser' => $otherUser,
            'canSend' => $canSend,
            'isBlocked' => $isBlocked,
        ]);
    }

    /**
     * Combined conversations list (for both sides)
     */
    public function conversations()
    {
        $user = auth()->user();

        $conversations = Inquiry::with([
                'user',
                'recipient',
                'messages' => function ($q) {
                    $q->latest()->limit(1);
                },
            ])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            })
            ->whereDoesntHave('blocks', function ($q) use ($user) {
                $q->where('active', true)
                  ->where(function ($sub) use ($user) {
                      $sub->where('blocker_id', $user->id)
                          ->orWhere('blocked_user_id', $user->id);
                  });
            })
            ->whereDoesntHave('reports', function ($q) use ($user) {
                $q->whereIn('status', ['open', 'under_review'])
                  ->where(function ($sub) use ($user) {
                      $sub->where('reporter_id', $user->id)
                          ->orWhere('reported_user_id', $user->id);
                  });
            })
            ->latest('updated_at')
            ->paginate(20);

        return view('chat.conversations', compact('conversations'));
    }

    public function send(Request $request, Inquiry $inquiry)
    {
        $user = auth()->user();
        $this->ensureParticipantAccess($inquiry, $user);
        $otherUser = $this->getOtherParticipant($inquiry, $user);
        $canSend = $this->canSendMessages($inquiry, $user, $otherUser);

        if (!$canSend) {
            return back()->withErrors(['chat' => 'You cannot send messages for this inquiry right now.']);
        }
        if ($this->isBlocked($inquiry->id, $user->id, $otherUser?->id)) {
            return back()->withErrors(['chat' => 'Chat is blocked for this inquiry.']);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        InquiryMessage::create([
            'inquiry_id' => $inquiry->id,
            'sender_id' => $user->id,
            'message' => $data['message'],
        ]);

        return back()->with('success', 'Message sent.');
    }

    public function report(Request $request, Inquiry $inquiry)
    {
        $user = auth()->user();
        $this->ensureParticipantAccess($inquiry, $user);
        $otherUser = $this->getOtherParticipant($inquiry, $user);

        $data = $request->validate([
            'reason' => 'required|string|in:abuse,spam,fake_lead,other',
            'details' => 'nullable|string|max:2000',
        ]);

        $alreadyReported = InquiryReport::where('inquiry_id', $inquiry->id)
            ->where('reporter_id', $user->id)
            ->whereIn('status', ['open', 'under_review'])
            ->exists();

        if ($alreadyReported) {
            return back()->withErrors(['chat' => 'You already have an active report for this inquiry.']);
        }

        InquiryReport::create([
            'inquiry_id' => $inquiry->id,
            'reporter_id' => $user->id,
            'reported_user_id' => $otherUser?->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => 'open',
            'compensation_requested' => $data['reason'] === 'fake_lead' && $user->id === $inquiry->recipient_id,
        ]);

        // Automatically block chat for fake_lead reports
        if ($data['reason'] === 'fake_lead') {
            InquiryBlock::create([
                'inquiry_id' => $inquiry->id,
                'blocker_id' => $user->id,
                'blocked_user_id' => $otherUser?->id,
                'reason' => 'Report pending investigation - Fake Lead',
                'active' => true,
            ]);
        }

        return back()->with('success', 'Report submitted. Admin will review this conversation.');
    }

    public function block(Request $request, Inquiry $inquiry)
    {
        $user = auth()->user();
        $this->ensureParticipantAccess($inquiry, $user);
        $otherUser = $this->getOtherParticipant($inquiry, $user);

        if (!$otherUser) {
            return back()->withErrors(['chat' => 'Cannot block for this inquiry.']);
        }

        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        InquiryBlock::updateOrCreate(
            [
                'inquiry_id' => $inquiry->id,
                'blocker_id' => $user->id,
                'blocked_user_id' => $otherUser->id,
            ],
            [
                'reason' => $data['reason'],
                'active' => true,
            ]
        );

        return back()->with('success', 'User blocked for this inquiry.');
    }

    private function getOtherParticipant(Inquiry $inquiry, User $currentUser): ?User
    {
        if ($currentUser->id === $inquiry->recipient_id) {
            return $inquiry->user;
        }

        return $inquiry->recipient;
    }

    private function ensureParticipantAccess(Inquiry $inquiry, User $currentUser): void
    {
        if ($currentUser->id !== $inquiry->recipient_id && $currentUser->id !== $inquiry->user_id) {
            abort(403, 'Unauthorized chat access.');
        }
    }

    private function canSendMessages(Inquiry $inquiry, User $currentUser, ?User $otherUser): bool
    {
        if (!$otherUser) {
            return false;
        }

        if ($currentUser->id === $inquiry->recipient_id) {
            return $this->recipientHasAccessToLead($currentUser->id, $inquiry);
        }

        if ($currentUser->id === $inquiry->user_id) {
            // Customer can always send messages to the recipient once the inquiry exists.
            // Lead access/unlock gating applies to the recipient (trainer/gym) side only.
            return true;
        }

        return false;
    }

    private function recipientHasAccessToLead(int $recipientId, Inquiry $inquiry): bool
    {
        if ($inquiry->recipient_id !== $recipientId) {
            return false;
        }

        if ($inquiry->status === 'viewed') {
            return true;
        }

        $hasUnlimited = Subscription::where('user_id', $recipientId)
            ->whereIn('plan_type', ['pro', 'business'])
            ->where('expires_at', '>', now())
            ->exists();

        if ($hasUnlimited) {
            return true;
        }

        // If not viewed and no active subscription, recipient cannot message.
        // Lead unlock happens by credits which sets status=viewed.
        return false;
    }

    private function isBlocked(int $inquiryId, int $userId, ?int $otherUserId): bool
    {
        if (!$otherUserId) {
            return false;
        }

        return InquiryBlock::where('inquiry_id', $inquiryId)
            ->where('active', true)
            ->where(function ($query) use ($userId, $otherUserId) {
                $query->where(function ($q) use ($userId, $otherUserId) {
                    $q->where('blocker_id', $userId)->where('blocked_user_id', $otherUserId);
                })->orWhere(function ($q) use ($userId, $otherUserId) {
                    $q->where('blocker_id', $otherUserId)->where('blocked_user_id', $userId);
                });
            })
            ->exists();
    }
}
