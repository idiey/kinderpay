<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Kindergarten;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Generate monthly recurring invoices for all active students in a kindergarten.
     */
    public function generateMonthlyInvoices(Kindergarten $kindergarten, ?string $month = null): int
    {
        $billingDate = Carbon::parse($month ?? now()->format('Y-m-01'))->startOfMonth();
        $billingMonth = $billingDate->toDateString();
        $dueDate = $billingDate->copy()->addDays(14)->toDateString();

        $activeStudents = Student::withoutGlobalScopes()
            ->where('kindergarten_id', $kindergarten->id)
            ->where('status', 'active')
            ->with(['studentFees.feeTemplate'])
            ->get();

        $generatedCount = 0;

        foreach ($activeStudents as $student) {
            // Check if invoice already exists for this student and month
            $exists = Invoice::withoutGlobalScopes()
                ->where('kindergarten_id', $kindergarten->id)
                ->where('student_id', $student->id)
                ->where('billing_month', $billingMonth)
                ->exists();

            if ($exists) {
                continue;
            }

            // Filter active recurring fees for this billing month
            $fees = $student->studentFees->filter(function ($sf) use ($billingMonth) {
                $fromOk = $sf->effective_from <= $billingMonth;
                $toOk = is_null($sf->effective_to) || $sf->effective_to >= $billingMonth;
                $activeTemplate = $sf->feeTemplate && $sf->feeTemplate->is_active;
                return $fromOk && $toOk && $activeTemplate;
            });

            if ($fees->isEmpty()) {
                continue;
            }

            DB::transaction(function () use ($kindergarten, $student, $billingMonth, $dueDate, $fees, &$generatedCount) {
                $invoiceNumber = $this->generateInvoiceNumber($kindergarten, $billingMonth);

                $subtotal = 0;
                $discountTotal = 0;
                $lineItemsData = [];

                foreach ($fees as $fee) {
                    $template = $fee->feeTemplate;
                    $unitPrice = (float) ($fee->custom_amount ?? $template->amount);
                    $discount = (float) $fee->discount_amount;
                    $lineTotal = max(0, $unitPrice - $discount);

                    $subtotal += $unitPrice;
                    $discountTotal += $discount;

                    $lineItemsData[] = [
                        'fee_template_id' => $template->id,
                        'description' => $template->name . ($fee->discount_reason ? " ({$fee->discount_reason})" : ''),
                        'quantity' => 1,
                        'unit_price' => $unitPrice,
                        'discount' => $discount,
                        'total' => $lineTotal,
                    ];
                }

                $totalAmount = max(0, $subtotal - $discountTotal);

                $invoice = Invoice::create([
                    'kindergarten_id' => $kindergarten->id,
                    'student_id' => $student->id,
                    'invoice_number' => $invoiceNumber,
                    'billing_month' => $billingMonth,
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'balance_due' => $totalAmount,
                    'status' => 'draft',
                    'due_date' => $dueDate,
                ]);

                foreach ($lineItemsData as $item) {
                    $item['invoice_id'] = $invoice->id;
                    InvoiceItem::create($item);
                }

                $generatedCount++;
            });
        }

        return $generatedCount;
    }

    /**
     * Create an ad-hoc or single invoice.
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $student = Student::findOrFail($data['student_id']);
            $kindergarten = $student->kindergarten;
            $billingMonth = Carbon::parse($data['billing_month'] ?? now()->format('Y-m-01'))->startOfMonth()->toDateString();
            $dueDate = $data['due_date'] ?? Carbon::parse($billingMonth)->addDays(14)->toDateString();
            $invoiceNumber = $data['invoice_number'] ?? $this->generateInvoiceNumber($kindergarten, $billingMonth);

            $subtotal = 0;
            $discountTotal = (float) ($data['discount_total'] ?? 0);

            $invoice = Invoice::create([
                'kindergarten_id' => $kindergarten->id,
                'student_id' => $student->id,
                'invoice_number' => $invoiceNumber,
                'billing_month' => $billingMonth,
                'subtotal' => 0, // updated below
                'discount_total' => $discountTotal,
                'total_amount' => 0,
                'paid_amount' => 0,
                'balance_due' => 0,
                'status' => $data['status'] ?? 'draft',
                'due_date' => $dueDate,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $lineTotal = max(0, ($qty * $unitPrice) - $discount);

                $subtotal += ($qty * $unitPrice);
                $discountTotal += $discount;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'fee_template_id' => $item['fee_template_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'total' => $lineTotal,
                ]);
            }

            $total = max(0, $subtotal - $discountTotal);
            $invoice->update([
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'total_amount' => $total,
                'balance_due' => $total,
            ]);

            return $invoice->fresh(['items', 'student']);
        });
    }

    /**
     * Generate sequential invoice number: INV-YYYYMM-XXXX
     */
    public function generateInvoiceNumber(Kindergarten $kindergarten, string $billingMonth): string
    {
        $prefix = ($kindergarten->invoice_prefix ?: 'INV') . '-' . Carbon::parse($billingMonth)->format('Ym') . '-';
        
        $latest = Invoice::withoutGlobalScopes()
            ->where('kindergarten_id', $kindergarten->id)
            ->where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->invoice_number, $matches)) {
            $seq = (int) $matches[1] + 1;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
