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
        $blockedInquiryIds = InquiryBlock::where('active', true)
            ->where(function ($query) use ($user) {
                $query->where('blocker_id', $user->id)
                    ->orWhere('blocked_user_id', $user->id);
            })
            ->pluck('inquiry_id')
            ->toArray();

        $inquiries = Inquiry::with('recipient')
            ->where('user_id', $user->id)
            ->whereNotIn('id', $blockedInquiryIds)
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
            'reason' => 'nullable|string|max:100',
        ]);

        InquiryBlock::updateOrCreate(
            [
                'inquiry_id' => $inquiry->id,
                'blocker_id' => $user->id,
                'blocked_user_id' => $otherUser->id,
            ],
            [
                'reason' => $data['reason'] ?? null,
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
            return $this->recipientHasAccessToLead($inquiry->recipient_id, $inquiry);
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
            ->whereIn('plan_type', ['monthly', 'yearly'])
            ->where('expires_at', '>', now())
            ->exists();

        if ($hasUnlimited) {
            return true;
        }

        return Payment::where('user_id', $recipientId)
            ->where('status', 'paid')
            ->where('plan_name', 'single_lead')
            ->where('context_type', 'lead_unlock')
            ->where('context_id', $inquiry->id)
            ->exists();
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
