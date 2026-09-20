<?php

namespace App\Http\Controllers;

use App\Services\BillplzService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected BillplzService $billplzService
    ) {}

    public function billplz(Request $request): JsonResponse
    {
        Log::info('Billplz webhook received', $request->all());

        $signature = $request->header('X-Signature', '');
        $data = $request->all();

        if ($signature && !$this->billplzService->verifySignature($data, $signature)) {
            Log::warning('Invalid Billplz webhook signature', ['signature' => $signature]);
            return response()->json(['status' => 'invalid signature'], 400);
        }

        $payment = $this->paymentService->handleWebhookCallback($data, $signature);

        if (!$payment) {
            return response()->json(['status' => 'ignored or not found'], 200);
        }

        return response()->json([
            'status' => 'success',
            'payment_id' => $payment->id,
            'payment_status' => $payment->status,
        ]);
    }
}
