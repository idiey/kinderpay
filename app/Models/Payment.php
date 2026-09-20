<?php

namespace App\Models;

use App\Models\Traits\BelongsToKindergarten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, BelongsToKindergarten;

    protected $fillable = [
        'kindergarten_id',
        'invoice_id',
        'amount',
        'method',
        'status',
        'gateway_ref',
        'gateway_response',
        'reference_number',
        'proof_path',
        'receipt_number',
        'paid_at',
        'recorded_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->receipt_number) && $payment->status === 'completed') {
                $prefix = 'RCP-' . date('Ym') . '-';
                $latest = static::where('receipt_number', 'like', "{$prefix}%")
                    ->orderBy('id', 'desc')
                    ->first();
                $seq = 1;
                if ($latest && preg_match('/-(\d+)$/', $latest->receipt_number, $matches)) {
                    $seq = (int) $matches[1] + 1;
                }
                $payment->receipt_number = $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
