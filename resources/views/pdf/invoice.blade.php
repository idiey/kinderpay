<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
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
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
        }
        .kindergarten-name {
            font-size: 22px;
            font-weight: bold;
            color: #4f46e5;
            margin: 0 0 5px 0;
        }
        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            text-align: right;
            color: #1f2937;
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
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .text-right {
            text-align: right;
        }
        .totals-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 10px;
        }
        .total-row td {
            border-top: 2px solid #e5e7eb;
            font-weight: bold;
            font-size: 15px;
            color: #1f2937;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-paid { background-color: #d1fae5; color: #065f46; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-overdue { background-color: #fee2e2; color: #991b1b; }
        .badge-partially_paid { background-color: #e0e7ff; color: #3730a3; }
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
                <div class="invoice-title">INVOICE</div>
                <div style="font-weight: bold; font-size: 14px; margin-top: 5px;">{{ $invoice->invoice_number }}</div>
                <div style="margin-top: 8px;">
                    <span class="badge badge-{{ $invoice->status }}">{{ strtoupper(str_replace('_', ' ', $invoice->status)) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td>
                <div class="section-title">Billed To (Parent / Student):</div>
                <div style="font-size: 14px; font-weight: bold;">{{ $student->name }}</div>
                @if($student->classGroup)
                    <div>Class: {{ $student->classGroup->name }}</div>
                @endif
                @if($student->guardians->isNotEmpty())
                    @php $g = $student->guardians->first(); @endphp
                    <div>Guardian: {{ $g->name }} ({{ ucfirst($g->pivot->relationship ?? 'Guardian') }})</div>
                    <div>Phone: {{ $g->phone }}</div>
                @endif
            </td>
            <td style="text-align: right;">
                <div class="section-title">Invoice Details:</div>
                <div><strong>Billing Month:</strong> {{ \Carbon\Carbon::parse($invoice->billing_month)->format('F Y') }}</div>
                <div><strong>Invoice Date:</strong> {{ $invoice->created_at->format('d M Y') }}</div>
                <div><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 55%;">Item Description</th>
                <th style="width: 10%;" class="text-right">Qty</th>
                <th style="width: 15%;" class="text-right">Unit Price (RM)</th>
                <th style="width: 15%;" class="text-right">Total (RM)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #9ca3af;">No fee items</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">RM {{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->discount_total > 0)
        <tr>
            <td style="color: #059669;">Discounts:</td>
            <td class="text-right" style="color: #059669;">- RM {{ number_format($invoice->discount_total, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>Total Amount:</td>
            <td class="text-right">RM {{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Paid Amount:</td>
            <td class="text-right" style="color: #059669;">RM {{ number_format($invoice->paid_amount, 2) }}</td>
        </tr>
        <tr style="font-weight: bold; color: {{ $invoice->balance_due > 0 ? '#b91c1c' : '#059669' }};">
            <td>Balance Due:</td>
            <td class="text-right">RM {{ number_format($invoice->balance_due, 2) }}</td>
        </tr>
    </table>

    @if($invoice->notes)
        <div style="margin-top: 30px; padding: 12px; background: #f9fafb; border-left: 3px solid #6366f1; border-radius: 4px;">
            <strong>Notes:</strong> {{ $invoice->notes }}
        </div>
    @endif

    <div class="footer">
        <p>Thank you for your prompt payment. This is a computer-generated document. No signature is required.</p>
        <p>Powered by KinderPay SaaS</p>
    </div>
</body>
</html>
