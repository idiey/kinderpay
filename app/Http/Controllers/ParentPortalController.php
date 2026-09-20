<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Services\PaymentService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ParentPortalController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected PdfService $pdfService
    ) {}

    /**
     * Helper to get student IDs linked to authenticated parent.
     */
    protected function getParentStudentIds(): array
    {
        $user = Auth::user();
        $guardian = Guardian::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        if (!$guardian) {
            return [];
        }

        return $guardian->students()->pluck('students.id')->toArray();
    }

    public function dashboard(): Response
    {
        $studentIds = $this->getParentStudentIds();

        $students = Student::whereIn('id', $studentIds)
            ->with(['classGroup'])
            ->get();

        $invoices = Invoice::whereIn('student_id', $studentIds)
            ->with(['student'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $outstandingBalance = Invoice::whereIn('student_id', $studentIds)
            ->unpaid()
            ->sum('balance_due');

        $totalPaid = Payment::whereHas('invoice', function ($q) use ($studentIds) {
                $q->whereIn('student_id', $studentIds);
            })
            ->where('status', 'completed')
            ->sum('amount');

        return Inertia::render('ParentPortal/Dashboard', [
            'students' => $students,
            'recent_invoices' => $invoices,
            'outstanding_balance' => (float) $outstandingBalance,
            'total_paid' => (float) $totalPaid,
        ]);
    }

    public function invoices(): Response
    {
        $studentIds = $this->getParentStudentIds();

        $invoices = Invoice::whereIn('student_id', $studentIds)
            ->with(['student.classGroup'])
            ->orderBy('billing_month', 'desc')
            ->paginate(10);

        return Inertia::render('ParentPortal/Invoices', [
            'invoices' => $invoices,
        ]);
    }

    public function showInvoice(Invoice $invoice): Response
    {
        $studentIds = $this->getParentStudentIds();
        if (!in_array($invoice->student_id, $studentIds) && !Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $invoice->load([
            'student.classGroup',
            'items',
            'payments' => function ($q) {
                $q->where('status', 'completed')->orderBy('id', 'desc');
            },
        ]);

        return Inertia::render('ParentPortal/InvoiceDetail', [
            'invoice' => $invoice,
        ]);
    }

    public function pay(Invoice $invoice): RedirectResponse
    {
        $studentIds = $this->getParentStudentIds();
        if (!in_array($invoice->student_id, $studentIds) && !Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($invoice->balance_due <= 0) {
            return back()->with('info', 'Invoice is already paid in full.');
        }

        try {
            $redirectUrl = route('parent.invoices.show', $invoice->id);
            $result = $this->paymentService->initiateOnlinePayment($invoice, $redirectUrl);

            // Redirect user directly to Billplz hosted payment gateway
            return Inertia::location($result['checkout_url']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function history(): Response
    {
        $studentIds = $this->getParentStudentIds();

        $payments = Payment::whereHas('invoice', function ($q) use ($studentIds) {
                $q->whereIn('student_id', $studentIds);
            })
            ->with(['invoice.student'])
            ->where('status', 'completed')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return Inertia::render('ParentPortal/History', [
            'payments' => $payments,
        ]);
    }

    public function downloadReceipt(Payment $payment): HttpResponse
    {
        $studentIds = $this->getParentStudentIds();
        if (!in_array($payment->invoice->student_id, $studentIds) && !Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403, 'Unauthorized access to receipt.');
        }

        return $this->pdfService->generateReceiptPdf($payment, false);
    }
}
