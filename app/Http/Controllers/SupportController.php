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
            'message' => 'required|string|max:3000',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'message' => $data['message'],
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
