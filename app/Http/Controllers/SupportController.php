<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tickets = SupportTicket::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('support.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'issue_type' => 'required|string|in:billing,login,kyc,leads,technical,feedback,other',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'message' => 'required|string|max:3000',
            'related_page' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-attachments', 'public');
        }

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'issue_type' => $data['issue_type'],
            'priority' => $data['priority'],
            'message' => $data['message'],
            'attachment_path' => $attachmentPath,
            'related_page' => $data['related_page'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'status' => 'open',
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'message' => $data['message'],
        ]);

        return redirect()->route('support.show', $ticket)->with('success', 'Support ticket created successfully.');
    }

    public function show(SupportTicket $ticket)
    {
        $user = auth()->user();
        if ($ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized support ticket access.');
        }

        $ticket->load(['messages.sender']);

        return view('support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $user = auth()->user();
        if ($ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized support ticket access.');
        }

        $data = $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'message' => $data['message'],
        ]);

        if ($ticket->status === 'resolved') {
            $ticket->update([
                'status' => 'open',
                'resolved_by' => null,
                'resolved_at' => null,
            ]);
        }

        return back()->with('success', 'Reply sent to support team.');
    }
}
