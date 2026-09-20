<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected PdfService $pdfService
    ) {}

    public function index(Request $request): Response
    {
        $query = Payment::with(['invoice.student.classGroup', 'recorder']);

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('invoice.student', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $statsQuery = clone $query;
        $totalCollected = $statsQuery->where('status', 'completed')->sum('amount');

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['method', 'status', 'search']),
            'total_collected' => $totalCollected,
        ]);
    }

    public function record(Invoice $invoice): Response
    {
        $invoice->load(['student.classGroup', 'student.guardians']);

        return Inertia::render('Payments/Record', [
            'invoice' => $invoice,
        ]);
    }

    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => "required|numeric|min:0.01|max:{$invoice->balance_due}",
            'method' => 'required|in:cash,bank_transfer,cheque',
            'paid_at' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $payment = $this->paymentService->recordManualPayment(
            $invoice,
            $validated,
            Auth::id()
        );

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', "Payment of RM {$payment->amount} recorded. Receipt: {$payment->receipt_number}");
    }

    public function downloadReceipt(Payment $payment): HttpResponse
    {
        return $this->pdfService->generateReceiptPdf($payment, false);
    }
}
