<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_template_id',
        'custom_amount',
        'discount_amount',
        'discount_reason',
        'effective_from',
        'effective_to',
    ];

    protected function casts(): array
    {
        return [
            'custom_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeTemplate(): BelongsTo
    {
        return $this->belongsTo(FeeTemplate::class);
    }

    /**
     * Get the final applicable amount.
     */
    public function getFinalAmountAttribute(): float
    {
        $base = $this->custom_amount ?? $this->feeTemplate?->amount ?? 0;
        return max(0, (float) $base - (float) $this->discount_amount);
    }
}
