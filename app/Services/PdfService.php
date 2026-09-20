<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfService
{
    /**
     * Generate and stream/download invoice PDF.
     */
    public function generateInvoicePdf(Invoice $invoice, bool $download = false): Response
    {
        $invoice->loadMissing(['student.guardians', 'items', 'kindergarten']);
        $kindergarten = $invoice->kindergarten;

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'kindergarten' => $kindergarten,
            'student' => $invoice->student,
            'items' => $invoice->items,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $fileName = "Invoice-{$invoice->invoice_number}.pdf";

        if ($download) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }

    /**
     * Generate and stream/download payment receipt PDF.
     */
    public function generateReceiptPdf(Payment $payment, bool $download = false): Response
    {
        $payment->loadMissing(['invoice.student.guardians', 'kindergarten']);
        $invoice = $payment->invoice;
        $kindergarten = $payment->kindergarten;

        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
            'invoice' => $invoice,
            'kindergarten' => $kindergarten,
            'student' => $invoice->student,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $fileName = "Receipt-{$payment->receipt_number}.pdf";

        if ($download) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }

    /**
     * Generate and stream/download payslip PDF.
     */
    public function generatePayslipPdf(\App\Models\PayrollItem $item, bool $download = false): Response
    {
        $item->loadMissing(['payrollRun.kindergarten', 'staff', 'allowanceItems.allowanceType']);
        $kindergarten = $item->payrollRun->kindergarten;

        $pdf = Pdf::loadView('pdf.payslip', [
            'item' => $item,
            'staff' => $item->staff,
            'payrollRun' => $item->payrollRun,
            'kindergarten' => $kindergarten,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $fileName = "Payslip-{$item->staff->name}-{$item->payrollRun->year}_{$item->payrollRun->month}.pdf";

        if ($download) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }
}
