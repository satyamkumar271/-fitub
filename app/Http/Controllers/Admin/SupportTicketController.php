<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'open');
        $allowedStatuses = ['open', 'in_progress', 'resolved', 'all'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'open';
        }

        $query = SupportTicket::with(['user', 'resolver'])->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $tickets = $query->paginate(25)->appends(['status' => $status]);

        return view('admin.support.index', compact('tickets', 'status'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'resolver', 'messages.sender']);

        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => auth()->id(),
            'message' => $data['message'],
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Reply sent to user.');
    }

    public function resolve(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'status' => 'required|string|in:open,in_progress,resolved',
        ]);

        $update = ['status' => $data['status']];
        if ($data['status'] === 'resolved') {
            $update['resolved_by'] = auth()->id();
            $update['resolved_at'] = now();
        } else {
            $update['resolved_by'] = null;
            $update['resolved_at'] = null;
        }

        $ticket->update($update);

        return back()->with('success', 'Ticket status updated.');
    }
}
