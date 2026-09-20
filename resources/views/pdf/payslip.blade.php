<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $staff->name }} - {{ $payrollRun->year }}/{{ $payrollRun->month }}</title>
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
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        .kindergarten-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 4px 0;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
            color: #1f2937;
            margin: 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px 8px;
            font-size: 12px;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .breakdown-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 12px;
        }
        .breakdown-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .net-pay-box {
            background-color: #eff6ff;
            border: 2px solid #bfdbfe;
            border-radius: 6px;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        .net-pay-amount {
            font-size: 26px;
            font-weight: bold;
            color: #1d4ed8;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="kindergarten-name">{{ $kindergarten->name }}</div>
                <div>{{ $kindergarten->address }} {{ $kindergarten->postcode }} {{ $kindergarten->city }}</div>
                <div>Tel: {{ $kindergarten->phone ?? 'N/A' }} | Email: {{ $kindergarten->email ?? 'N/A' }}</div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <div class="title">PAYSLIP / PENYATA GAJI</div>
                <div style="font-weight: bold; font-size: 14px; margin-top: 4px;">Period: {{ \Carbon\Carbon::createFromDate($payrollRun->year, $payrollRun->month, 1)->format('F Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="width: 20%; font-weight: bold; color: #64748b;">Staff Name:</td>
            <td style="width: 30%; font-weight: bold; color: #0f172a;">{{ $staff->name }}</td>
            <td style="width: 20%; font-weight: bold; color: #64748b;">Designation:</td>
            <td style="width: 30%;">{{ $staff->position }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #64748b;">Employee ID:</td>
            <td>{{ $staff->employee_id ?? 'N/A' }}</td>
            <td style="font-weight: bold; color: #64748b;">Employment Type:</td>
            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $staff->employment_type) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #64748b;">Bank Name:</td>
            <td>{{ $staff->bank_name ?? 'N/A' }}</td>
            <td style="font-weight: bold; color: #64748b;">Bank Account:</td>
            <td>{{ $staff->bank_account ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="breakdown-table">
        <tr>
            <th style="width: 50%;">Earnings / Pendapatan</th>
            <th style="width: 50%;">Deductions / Potongan</th>
        </tr>
        <tr>
            <td>
                <table style="width: 100%;">
                    <tr>
                        <td>Basic Salary:</td>
                        <td style="text-align: right;">RM {{ number_format($item->basic_salary, 2) }}</td>
                    </tr>
                    @foreach($item->allowanceItems as $ai)
                    <tr>
                        <td>{{ $ai->allowanceType->name }}:</td>
                        <td style="text-align: right;">RM {{ number_format($ai->amount, 2) }}</td>
                    </tr>
                    @endforeach
                    @if($item->overtime_amount > 0)
                    <tr>
                        <td>Overtime:</td>
                        <td style="text-align: right;">RM {{ number_format($item->overtime_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->bonus_amount > 0)
                    <tr>
                        <td>Bonus / Incentive:</td>
                        <td style="text-align: right;">RM {{ number_format($item->bonus_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->unpaid_leave_deduction > 0)
                    <tr style="color: #dc2626;">
                        <td>Unpaid Leave ({{ $item->unpaid_leave_days }} days):</td>
                        <td style="text-align: right;">- RM {{ number_format($item->unpaid_leave_deduction, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 1px solid #cbd5e1; font-weight: bold;">
                        <td style="padding-top: 8px;">Gross Earnings:</td>
                        <td style="text-align: right; padding-top: 8px;">RM {{ number_format($item->adjusted_gross, 2) }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table style="width: 100%;">
                    <tr>
                        <td>EPF Employee (11%):</td>
                        <td style="text-align: right;">RM {{ number_format($item->epf_employee, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SOCSO Employee:</td>
                        <td style="text-align: right;">RM {{ number_format($item->socso_employee, 2) }}</td>
                    </tr>
                    <tr>
                        <td>EIS Employee:</td>
                        <td style="text-align: right;">RM {{ number_format($item->eis_employee, 2) }}</td>
                    </tr>
                    @if($item->pcb_amount > 0)
                    <tr>
                        <td>PCB (Tax):</td>
                        <td style="text-align: right;">RM {{ number_format($item->pcb_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->other_deductions > 0)
                    <tr>
                        <td>Other Deductions:</td>
                        <td style="text-align: right;">RM {{ number_format($item->other_deductions, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 1px solid #cbd5e1; font-weight: bold;">
                        <td style="padding-top: 8px;">Total Deductions:</td>
                        <td style="text-align: right; padding-top: 8px; color: #dc2626;">RM {{ number_format($item->total_deductions, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="net-pay-box">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div style="font-size: 12px; text-transform: uppercase; color: #1e40af; font-weight: bold;">Net Pay / Gaji Bersih</div>
                    <div class="net-pay-amount">RM {{ number_format($item->net_pay, 2) }}</div>
                </td>
                <td style="text-align: right; font-size: 11px; color: #64748b;">
                    <div><strong>Employer EPF:</strong> RM {{ number_format($item->epf_employer, 2) }}</div>
                    <div><strong>Employer SOCSO:</strong> RM {{ number_format($item->socso_employer, 2) }}</div>
                    <div><strong>Employer EIS:</strong> RM {{ number_format($item->eis_employer, 2) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated payslip. No signature is required.</p>
        <p>Penyata gaji ini dijana secara automatik oleh sistem KinderPay.</p>
    </div>
</body>
</html>
