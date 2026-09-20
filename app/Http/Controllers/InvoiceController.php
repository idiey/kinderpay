<?php

namespace App\Http\Controllers;

use App\Models\FeeTemplate;
use App\Models\Invoice;
use App\Models\Student;
use App\Services\InvoiceService;
use App\Services\PdfService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected PdfService $pdfService
    ) {}

    public function index(Request $request): Response
    {
        $query = Invoice::with(['student.classGroup']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('billing_month', Carbon::parse($request->month . '-01')->toDateString());
        }

        $invoices = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Summary totals for currently filtered results
        $statsQuery = clone $query;
        $totalBilled = $statsQuery->sum('total_amount');
        $totalPaid = $statsQuery->sum('paid_amount');
        $totalOutstanding = $statsQuery->sum('balance_due');

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status', 'month']),
            'stats' => [
                'total_billed' => $totalBilled,
                'total_paid' => $totalPaid,
                'total_outstanding' => $totalOutstanding,
            ],
        ]);
    }

    public function create(): Response
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $templates = FeeTemplate::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Invoices/Create', [
            'students' => $students,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'billing_month' => 'required|date',
            'due_date' => 'required|date|after_or_equal:billing_month',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.fee_template_id' => 'nullable|exists:fee_templates,id',
        ]);

        $invoice = $this->invoiceService->createInvoice($validated);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load([
            'student.classGroup',
            'student.guardians',
            'items',
            'payments.recorder',
        ]);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $kindergarten = Auth::user()->kindergarten;
        if (!$kindergarten) {
            return back()->with('error', 'No active kindergarten found for user.');
        }

        $count = $this->invoiceService->generateMonthlyInvoices($kindergarten, $request->month ? $request->month . '-01' : null);

        return redirect()->route('invoices.index')
            ->with('success', "Generated {$count} recurring invoices successfully.");
    }

    public function send(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === 'draft') {
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return back()->with('success', "Invoice marked as sent.");
    }

    public function downloadPdf(Invoice $invoice): HttpResponse
    {
        return $this->pdfService->generateInvoicePdf($invoice, false);
    }
}
