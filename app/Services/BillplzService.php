<?php

namespace App\Services;

use App\Models\Kindergarten;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillplzService
{
    protected string $baseUrl;

    public function __construct()
    {
        $sandbox = config('services.billplz.sandbox', true);
        $this->baseUrl = $sandbox
            ? 'https://www.billplz-sandbox.com/api'
            : 'https://www.billplz.com/api';
    }

    /**
     * Create a bill on Billplz.
     *
     * @param array $params [email, mobile, name, amount (in RM), callback_url, redirect_url, description]
     * @param Kindergarten|null $kindergarten
     * @return array [success => bool, bill_id => string, url => string, raw => array]
     */
    public function createBill(array $params, ?Kindergarten $kindergarten = null): array
    {
        $apiKey = $kindergarten?->gateway_api_key ?: config('services.billplz.api_key', 'mock-key');
        $collectionId = $kindergarten?->gateway_collection_id ?: config('services.billplz.collection_id', 'mock-collection');

        // Billplz expects amount in sen (cents)
        $amountInSen = (int) round(((float) $params['amount']) * 100);

        try {
            $response = Http::withBasicAuth($apiKey, '')
                ->post("{$this->baseUrl}/v3/bills", [
                    'collection_id' => $collectionId,
                    'email' => $params['email'] ?? 'parent@kinderpay.test',
                    'mobile' => $params['mobile'] ?? '60123456789',
                    'name' => $params['name'] ?? 'Parent',
                    'amount' => $amountInSen,
                    'callback_url' => $params['callback_url'],
                    'redirect_url' => $params['redirect_url'] ?? null,
                    'description' => substr($params['description'] ?? 'Kindergarten Fee', 0, 200),
                    'reference_1_label' => 'Invoice No',
                    'reference_1' => $params['reference_1'] ?? '',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'bill_id' => $data['id'],
                    'url' => $data['url'],
                    'raw' => $data,
                ];
            }

            Log::error('Billplz bill creation failed', ['body' => $response->body()]);
            return [
                'success' => false,
                'error' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Billplz exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify Billplz webhook X-Signature.
     */
    public function verifySignature(array $data, string $signature, ?string $xSignatureKey = null): bool
    {
        $key = $xSignatureKey ?: config('services.billplz.x_signature_key');
        if (empty($key)) {
            return true; // if no key configured in dev/testing, allow
        }

        // Billplz signature: hmac sha256 of flattened keys
        $source = "amount" . ($data['amount'] ?? '')
            . "|id" . ($data['id'] ?? '')
            . "|paid" . ($data['paid'] ?? '')
            . "|paid_at" . ($data['paid_at'] ?? '');

        $calculated = hash_hmac('sha256', $source, $key);

        return hash_equals($calculated, $signature);
    }
}
