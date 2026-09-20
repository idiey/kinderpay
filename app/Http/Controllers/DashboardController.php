<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $currentMonth = now()->startOfMonth()->toDateString();

        $activeStudentsCount = Student::where('status', 'active')->count();
        $classesCount = ClassGroup::count();

        // Financials for this month
        $thisMonthBilled = Invoice::where('billing_month', $currentMonth)->sum('total_amount');
        $thisMonthCollected = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $totalOutstanding = Invoice::unpaid()->sum('balance_due');
        $overdueCount = Invoice::overdue()->count();

        $recentInvoices = Invoice::with(['student'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['invoice.student'])
            ->where('status', 'completed')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'active_students' => $activeStudentsCount,
                'classes' => $classesCount,
                'this_month_billed' => (float) $thisMonthBilled,
                'this_month_collected' => (float) $thisMonthCollected,
                'total_outstanding' => (float) $totalOutstanding,
                'overdue_count' => $overdueCount,
            ],
            'recent_invoices' => $recentInvoices,
            'recent_payments' => $recentPayments,
        ]);
    }
}
