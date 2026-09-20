<?php

namespace App\Http\Controllers;

use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Services\PayrollService;
use App\Services\PdfService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService,
        protected PdfService $pdfService
    ) {}

    public function index(): Response
    {
        $runs = PayrollRun::withCount('items')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        return Inertia::render('Payroll/Index', [
            'runs' => $runs,
        ]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2035',
        ]);

        $kindergarten = Auth::user()->kindergarten;

        try {
            $run = $this->payrollService->generatePayrollRun(
                $kindergarten,
                (int) $validated['month'],
                (int) $validated['year']
            );

            return redirect()->route('payroll.show', $run->id)->with('success', 'Payroll calculated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(PayrollRun $payrollRun): Response
    {
        $payrollRun->load([
            'items.staff',
            'items.allowanceItems.allowanceType',
            'confirmer',
        ]);

        return Inertia::render('Payroll/Show', [
            'run' => $payrollRun,
        ]);
    }

    public function confirm(PayrollRun $payrollRun): RedirectResponse
    {
        try {
            $this->payrollService->confirmPayrollRun($payrollRun, Auth::id());

            return back()->with('success', 'Payroll run confirmed and finalized.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadPayslip(PayrollItem $payrollItem): HttpResponse
    {
        return $this->pdfService->generatePayslipPdf($payrollItem, false);
    }
}
