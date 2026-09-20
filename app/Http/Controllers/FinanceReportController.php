<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PayrollRun;
use App\Models\Student;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class FinanceReportController extends Controller
{
    public function index(): Response
    {
        $year = now()->year;

        // Monthly revenue collected this year
        $monthlyRevenue = [];
        $monthlyPayroll = [];

        for ($m = 1; $m <= 12; $m++) {
            $startDate = Carbon::createFromDate($year, $m, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $m, 1)->endOfMonth();

            $revenue = (float) Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->sum('amount');

            $payroll = (float) PayrollRun::where('year', $year)
                ->where('month', $m)
                ->where('status', 'confirmed')
                ->sum('total_net');

            $monthlyRevenue[] = $revenue;
            $monthlyPayroll[] = $payroll;
        }

        $totalRevenueYear = array_sum($monthlyRevenue);
        $totalPayrollYear = array_sum($monthlyPayroll);
        $netOperatingMargin = $totalRevenueYear - $totalPayrollYear;

        // Ageing Summary (Overdue buckets)
        $overdue0to30 = (float) Invoice::where('status', 'overdue')
            ->where('due_date', '>=', now()->subDays(30)->toDateString())
            ->sum('balance_due');

        $overdue31to60 = (float) Invoice::where('status', 'overdue')
            ->whereBetween('due_date', [now()->subDays(60)->toDateString(), now()->subDays(31)->toDateString()])
            ->sum('balance_due');

        $overdue60plus = (float) Invoice::where('status', 'overdue')
            ->where('due_date', '<', now()->subDays(60)->toDateString())
            ->sum('balance_due');

        return Inertia::render('Reports/Finance', [
            'year' => $year,
            'summary' => [
                'total_revenue' => $totalRevenueYear,
                'total_payroll' => $totalPayrollYear,
                'net_margin' => $netOperatingMargin,
            ],
            'monthly_revenue' => $monthlyRevenue,
            'monthly_payroll' => $monthlyPayroll,
            'ageing' => [
                'current_to_30' => $overdue0to30,
                'days_31_to_60' => $overdue31to60,
                'days_60_plus' => $overdue60plus,
            ],
        ]);
    }
}
