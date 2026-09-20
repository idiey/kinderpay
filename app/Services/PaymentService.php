<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        protected BillplzService $billplz
    ) {}

    /**
     * Record a manual payment (Cash, Bank Transfer, Cheque).
     */
    public function recordManualPayment(Invoice $invoice, array $data, ?int $userId = null): Payment
    {
        return DB::transaction(function () use ($invoice, $data, $userId) {
            $amount = (float) $data['amount'];
            $paidAt = isset($data['paid_at']) ? Carbon::parse($data['paid_at']) : now();

            $payment = Payment::create([
                'kindergarten_id' => $invoice->kindergarten_id,
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'method' => $data['method'] ?? 'cash',
                'status' => 'completed',
                'reference_number' => $data['reference_number'] ?? null,
                'proof_path' => $data['proof_path'] ?? null,
                'paid_at' => $paidAt,
                'recorded_by' => $userId,
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->recalculateBalances();

            return $payment;
        });
    }

    /**
     * Initiate online payment for an invoice via Billplz FPX.
     */
    public function initiateOnlinePayment(Invoice $invoice, ?string $redirectUrl = null): array
    {
        $amount = (float) $invoice->balance_due;
        if ($amount <= 0) {
            throw new \Exception("Invoice {$invoice->invoice_number} is already paid in full.");
        }

        $student = $invoice->student;
        $primaryGuardian = $student->guardians->first();

        $callbackUrl = route('api.webhooks.billplz');
        $redirectUrl = $redirectUrl ?? route('parent.invoices.show', $invoice->id);

        $billParams = [
            'amount' => $amount,
            'email' => $primaryGuardian?->email ?? 'parent@kinderpay.test',
            'mobile' => $primaryGuardian?->phone ?? '60123456789',
            'name' => $primaryGuardian?->name ?? $student->name,
            'callback_url' => $callbackUrl,
            'redirect_url' => $redirectUrl,
            'description' => "Invoice {$invoice->invoice_number} - {$student->name}",
            'reference_1' => $invoice->invoice_number,
        ];

        $billResponse = $this->billplz->createBill($billParams, $invoice->kindergarten);

        if (!$billResponse['success']) {
            throw new \Exception("Failed to initiate gateway payment: " . ($billResponse['error'] ?? 'Unknown error'));
        }

        // Create pending payment record
        $payment = Payment::create([
            'kindergarten_id' => $invoice->kindergarten_id,
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'method' => 'fpx',
            'status' => 'pending',
            'gateway_ref' => $billResponse['bill_id'],
            'gateway_response' => $billResponse['raw'],
            'notes' => 'Initiated via Billplz FPX',
        ]);

        return [
            'payment' => $payment,
            'checkout_url' => $billResponse['url'],
        ];
    }

    /**
     * Handle Billplz webhook callback.
     */
    public function handleWebhookCallback(array $payload, ?string $signature = null): ?Payment
    {
        $billId = $payload['id'] ?? null;
        if (!$billId) {
            Log::warning('Billplz webhook received without bill id', $payload);
            return null;
        }

        $payment = Payment::withoutGlobalScopes()->where('gateway_ref', $billId)->first();
        if (!$payment) {
            Log::warning("Payment not found for Billplz bill id: {$billId}");
            return null;
        }

        $isPaid = ($payload['paid'] ?? null) === 'true' || ($payload['paid'] ?? null) === true;

        return DB::transaction(function () use ($payment, $payload, $isPaid) {
            if ($isPaid) {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => isset($payload['paid_at']) ? Carbon::parse($payload['paid_at']) : now(),
                    'gateway_response' => $payload,
                ]);

                $invoice = $payment->invoice;
                $invoice->recalculateBalances();
            } else {
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => $payload,
                ]);
            }

            return $payment->fresh();
        });
    }
}
