<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Admin: Generate/Download invoice for any payment
     */
    public function adminDownload(Payment $payment)
    {
        if (!auth()->user() || auth()->user()->user_type !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        return $this->generatePdf($payment);
    }

    /**
     * User: Download invoice for own paid payment only
     */
    public function userDownload(Payment $payment)
    {
        $user = auth()->user();
        if (!$user || $payment->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        if ($payment->status !== 'paid') {
            return back()->with('error', 'Invoice is available only for paid payments.');
        }

        return $this->generatePdf($payment);
    }

    private function generatePdf(Payment $payment)
    {
        $payment->load(['user', 'user.gym']);

        $companyName = config('services.invoice.company_name', 'Fitub');
        $companyGst = config('services.invoice.gst_number', '');
        $companyAddress = [
            'line1' => (string) config('services.invoice.address_line1', ''),
            'line2' => (string) config('services.invoice.address_line2', ''),
            'city' => (string) config('services.invoice.city', ''),
            'state' => (string) config('services.invoice.state', ''),
            'pincode' => (string) config('services.invoice.pincode', ''),
        ];

        $baseAmount = $payment->base_amount ?? $payment->amount;
        $gstRate = $payment->gst_rate ?? config('services.invoice.gst_rate', 18);
        $gstAmount = $payment->gst_amount ?? 0;
        $totalAmount = $payment->amount;

        $buyerGymGst = null;
        $buyerGymAddress = null;
        if (($payment->user?->user_type ?? null) === 'gymowner' && $payment->user?->gym) {
            $gym = $payment->user->gym;
            $buyerGymGst = $gym->gst_number ?: null;
            $buyerGymAddress = [
                'line1' => (string) ($gym->address_street ?? ''),
                'city' => (string) ($gym->address_city ?? ''),
                'state' => (string) ($gym->address_state ?? ''),
                'pincode' => (string) ($gym->address_pincode ?? ''),
            ];
        }

        $pdf = Pdf::loadView('invoice.template', [
            'payment' => $payment,
            'companyName' => $companyName,
            'companyGst' => $companyGst,
            'companyAddress' => $companyAddress,
            'buyerGymGst' => $buyerGymGst,
            'buyerGymAddress' => $buyerGymAddress,
            'baseAmount' => $baseAmount,
            'gstRate' => $gstRate,
            'gstAmount' => $gstAmount,
            'totalAmount' => $totalAmount,
        ]);

        $filename = 'invoice-' . $payment->id . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
