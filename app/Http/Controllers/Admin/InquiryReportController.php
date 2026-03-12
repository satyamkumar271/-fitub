<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InquiryMessage;
use App\Models\InquiryReport;
use App\Models\UnlockCreditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InquiryReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'open');
        $allowed = ['open', 'under_review', 'resolved_valid', 'resolved_rejected', 'all'];
        if (!in_array($status, $allowed, true)) {
            $status = 'open';
        }

        $query = InquiryReport::with(['inquiry.recipient', 'inquiry.user', 'reporter', 'reportedUser', 'resolver'])->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->paginate(20)->appends(['status' => $status]);

        return view('admin.reports.index', compact('reports', 'status'));
    }

    public function show(InquiryReport $report)
    {
        $report->load(['inquiry.recipient', 'inquiry.user', 'reporter', 'reportedUser', 'resolver']);

        $messages = InquiryMessage::with('sender')
            ->where('inquiry_id', $report->inquiry_id)
            ->oldest()
            ->get();

        $creditLog = UnlockCreditLog::where('source_type', 'inquiry_report_compensation')
            ->where('source_id', $report->id)
            ->where('delta', 1)
            ->latest()
            ->first();

        return view('admin.reports.show', compact('report', 'messages', 'creditLog'));
    }

    public function resolve(Request $request, InquiryReport $report)
    {
        $data = $request->validate([
            'action' => 'required|string|in:valid,rejected,under_review',
            'admin_note' => 'nullable|string|max:2000',
            'grant_credit' => 'nullable|boolean',
        ]);

        if ($data['action'] === 'under_review') {
            $report->update([
                'status' => 'under_review',
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            return back()->with('success', 'Report marked under review.');
        }

        $status = $data['action'] === 'valid' ? 'resolved_valid' : 'resolved_rejected';

        $report->update([
            'status' => $status,
            'admin_note' => $data['admin_note'] ?? null,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        if (
            $status === 'resolved_valid'
            && $report->compensation_requested
            && $request->boolean('grant_credit')
            && $report->reporter
            && in_array($report->reporter->user_type, ['trainer', 'gymowner'], true)
        ) {
            DB::transaction(function () use ($report) {
                $reporter = User::where('id', $report->reporter_id)->lockForUpdate()->first();
                if (!$reporter) {
                    return;
                }

                $alreadyGranted = UnlockCreditLog::where('user_id', $reporter->id)
                    ->where('source_type', 'inquiry_report_compensation')
                    ->where('source_id', $report->id)
                    ->where('delta', 1)
                    ->exists();

                if ($alreadyGranted) {
                    return;
                }

                $reporter->increment('unlock_credits', 1);
                $reporter->refresh();

                UnlockCreditLog::create([
                    'user_id' => $reporter->id,
                    'delta' => 1,
                    'balance_after' => (int) $reporter->unlock_credits,
                    'source_type' => 'inquiry_report_compensation',
                    'source_id' => $report->id,
                    'note' => 'Compensation credit granted after report marked valid',
                    'created_by' => auth()->id(),
                ]);
            });
        }

        return back()->with('success', 'Report resolved successfully.');
    }
}
