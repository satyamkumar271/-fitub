<?php

namespace App\Http\Controllers\Admin;

use App\Models\InquiryBlock;
use App\Models\User;
use App\Mail\UserWarning;
use App\Mail\RegistrationCancelled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InquiryBlockController extends Controller
{
    public function index()
    {
        $blocks = InquiryBlock::with([
            'inquiry.user',
            'inquiry.recipient',
            'blocker',
            'blockedUser'
        ])
            ->where('active', true)
            ->latest()
            ->paginate(20);

        return view('admin.blocks.index', compact('blocks'));
    }

    public function show(InquiryBlock $block)
    {
        $block->load([
            'inquiry.user',
            'inquiry.recipient',
            'blocker',
            'blockedUser'
        ]);

        return view('admin.blocks.show', compact('block'));
    }

    public function sendWarning(Request $request, InquiryBlock $block)
    {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = $block->blockedUser;
        
        try {
            // Send warning notification
            Mail::to($user->email)->send(new UserWarning($user, $data['message']));
            
            // Log the warning
            DB::table('inquiry_block_warnings')->insert([
                'block_id' => $block->id,
                'admin_id' => auth()->id(),
                'message' => $data['message'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->with('success', 'Warning sent to ' . $user->name);
        } catch (\Exception $e) {
            \Log::error('Warning email failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send warning: ' . $e->getMessage());
        }
    }

    public function cancelRegistration(Request $request, InquiryBlock $block)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $user = $block->blockedUser;

        try {
            // Soft delete user
           $user->update([
    'registration_cancelled_at' => now(),
    'registration_cancellation_reason' => $data['reason'],
    'status' => 'cancelled', // add this
]);

            // Send cancellation email
            Mail::to($user->email)->send(new RegistrationCancelled($user, $data['reason']));

            // Update all blocks for this user to inactive
            InquiryBlock::where('blocked_user_id', $user->id)
                ->update(['active' => false]);

            // Log the action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'registration_cancelled',
                'user_id' => $user->id,
                'reason' => $data['reason'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.blocks.index')
                ->with('success', 'Registration cancelled for ' . $user->name);
        } catch (\Exception $e) {
            \Log::error('Registration cancellation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel registration: ' . $e->getMessage());
        }
    }

    public function unblock(InquiryBlock $block)
    {
        $block->update(['active' => false]);

        // Log the action
        DB::table('admin_logs')->insert([
            'admin_id' => auth()->id(),
            'action' => 'block_removed',
            'block_id' => $block->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Block removed successfully');
    }
}
