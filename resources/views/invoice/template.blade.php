<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice #{{ $payment->id }}</title>

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #333;
        background: #f9fafb;
        padding: 20px;
    }

    .container {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    .header {
        display: flex;
        justify-content: space-between;
        border-bottom: 2px solid #4f46e5;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .company {
        max-width: 60%;
    }

    .company-name {
        font-size: 22px;
        font-weight: bold;
        color: #4f46e5;
    }

    .gst {
        font-size: 11px;
        color: #666;
        margin-top: 4px;
    }

    .invoice-box {
        text-align: right;
    }

    .invoice-title {
        font-size: 20px;
        font-weight: bold;
        color: #111827;
    }

    .badge {
        background: #4f46e5;
        color: #fff;
        padding: 4px 10px;
        border-radius: 5px;
        font-size: 11px;
        display: inline-block;
        margin-top: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th {
        background: #f3f4f6;
        padding: 10px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .section-title {
        margin-top: 20px;
        font-size: 14px;
        font-weight: bold;
        color: #111827;
    }

    .card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
        margin-top: 10px;
    }

    .text-right {
        text-align: right;
    }

    .totals {
        margin-top: 25px;
        width: 300px;
        float: right;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    .totals table {
        margin: 0;
    }

    .totals td {
        padding: 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .totals .highlight {
        background: #eef2ff;
        font-weight: bold;
    }

    .totals .total {
        background: #4f46e5;
        color: #fff;
        font-size: 14px;
        font-weight: bold;
    }

    .footer {
        margin-top: 80px;
        text-align: center;
        font-size: 10px;
        color: #6b7280;
        border-top: 1px solid #e5e7eb;
        padding-top: 10px;
    }
</style>
</head>

<body>

<div class="container">

    <!-- Header -->
    <div class="header">
        <div class="company">
            <div class="company-name">{{ $companyName }}</div>

            @if($companyGst)
                <div class="gst">GSTIN: {{ $companyGst }}</div>
            @endif

            @php
                $addrParts = array_filter([
                    $companyAddress['line1'] ?? null,
                    $companyAddress['line2'] ?? null,
                    trim(implode(', ', array_filter([
                        $companyAddress['city'] ?? null,
                        $companyAddress['state'] ?? null,
                    ]))),
                    $companyAddress['pincode'] ?? null,
                ]);
            @endphp

            @if(!empty($addrParts))
                <div class="gst">{{ implode(' | ', $addrParts) }}</div>
            @endif
        </div>

        <div class="invoice-box">
            <div class="invoice-title">INVOICE</div>
            <div class="badge">#{{ $payment->id }}</div>
            <div style="margin-top:10px;">
                Date: {{ $payment->created_at?->format('d M Y') }}
            </div>
        </div>
    </div>

    <!-- Billing -->
    <div class="section-title">Bill To</div>
    <div class="card">
        <strong>{{ $payment->user->name ?? 'N/A' }}</strong><br>
        {{ $payment->user->email ?? 'N/A' }}

        @if(!empty($buyerGymGst))
            <br>GSTIN: {{ $buyerGymGst }}
        @endif

        @if(!empty($buyerGymAddress))
            @php
                $gymAddr = array_filter([
                    $buyerGymAddress['line1'] ?? null,
                    trim(implode(', ', array_filter([
                        $buyerGymAddress['city'] ?? null,
                        $buyerGymAddress['state'] ?? null,
                    ]))),
                    $buyerGymAddress['pincode'] ?? null,
                ]);
            @endphp
            <br>{{ implode(' | ', $gymAddr) }}
        @endif
    </div>

    <!-- Item Table -->
    <div class="section-title">Item Details</div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Base (₹)</th>
                <th class="text-right">GST ({{ $gstRate }}%)</th>
                <th class="text-right">Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->plan_name)) }} Plan</td>
                <td class="text-right">{{ number_format($baseAmount, 2) }}</td>
                <td class="text-right">{{ number_format($gstAmount, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">₹ {{ number_format($baseAmount, 2) }}</td>
            </tr>
            <tr>
                <td>GST</td>
                <td class="text-right">₹ {{ number_format($gstAmount, 2) }}</td>
            </tr>
            <tr class="total">
                <td>Total</td>
                <td class="text-right">₹ {{ number_format($totalAmount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <!-- Footer -->
    <div class="footer">
        Thank you for your business.<br>
        This is a computer-generated invoice.
    </div>

</div>

</body>
</html>