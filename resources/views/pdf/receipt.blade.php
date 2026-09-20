<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Receipt - {{ $payment->receipt_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 2px solid #059669;
            padding-bottom: 15px;
        }
        .kindergarten-name {
            font-size: 22px;
            font-weight: bold;
            color: #059669;
            margin: 0 0 5px 0;
        }
        .receipt-title {
            font-size: 26px;
            font-weight: bold;
            text-align: right;
            color: #065f46;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .meta-table td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .payment-box {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        .payment-amount {
            font-size: 24px;
            font-weight: bold;
            color: #065f46;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .details-table td:first-child {
            font-weight: bold;
            color: #4b5563;
            width: 35%;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="kindergarten-name">{{ $kindergarten->name }}</div>
                @if($kindergarten->registration_no)
                    <div>SSM/Reg: {{ $kindergarten->registration_no }}</div>
                @endif
                <div>{{ $kindergarten->address }} {{ $kindergarten->postcode }} {{ $kindergarten->city }} {{ $kindergarten->state }}</div>
                <div>Tel: {{ $kindergarten->phone ?? 'N/A' }} | Email: {{ $kindergarten->email ?? 'N/A' }}</div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <div class="receipt-title">OFFICIAL RECEIPT</div>
                <div style="font-weight: bold; font-size: 14px; margin-top: 5px;">{{ $payment->receipt_number }}</div>
                <div style="color: #059669; font-weight: bold; margin-top: 4px;">PAID / SELESAI</div>
            </td>
        </tr>
    </table>

    <div class="payment-box">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div style="font-size: 12px; text-transform: uppercase; color: #047857; font-weight: bold;">Amount Received / Jumlah Diterima</div>
                    <div class="payment-amount">RM {{ number_format($payment->amount, 2) }}</div>
                </td>
                <td style="text-align: right;">
                    <div><strong>Date Paid:</strong> {{ $payment->paid_at ? $payment->paid_at->format('d M Y, h:i A') : $payment->created_at->format('d M Y') }}</div>
                    <div><strong>Payment Method:</strong> {{ strtoupper($payment->method) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="details-table">
        <tr>
            <td>Student Name / Nama Pelajar:</td>
            <td><strong>{{ $student->name }}</strong></td>
        </tr>
        @if($student->classGroup)
        <tr>
            <td>Class / Kelas:</td>
            <td>{{ $student->classGroup->name }}</td>
        </tr>
        @endif
        <tr>
            <td>Invoice Reference / Rujukan Invois:</td>
            <td>{{ $invoice->invoice_number }} (Billing Month: {{ \Carbon\Carbon::parse($invoice->billing_month)->format('F Y') }})</td>
        </tr>
        @if($payment->reference_number)
        <tr>
            <td>Transaction / Reference No:</td>
            <td>{{ $payment->reference_number }}</td>
        </tr>
        @endif
        @if($payment->gateway_ref)
        <tr>
            <td>Gateway Bill ID:</td>
            <td>{{ $payment->gateway_ref }}</td>
        </tr>
        @endif
        <tr>
            <td>Invoice Total / Jumlah Invois:</td>
            <td>RM {{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Remaining Invoice Balance / Baki:</td>
            <td>RM {{ number_format($invoice->balance_due, 2) }}</td>
        </tr>
        @if($payment->notes)
        <tr>
            <td>Notes / Catatan:</td>
            <td>{{ $payment->notes }}</td>
        </tr>
        @endif
    </table>

    <div class="footer">
        <p>This is an official computer-generated receipt. No signature is required.</p>
        <p>Resit ini dijana secara automatik oleh sistem KinderPay.</p>
    </div>
</body>
</html>
