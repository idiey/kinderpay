<?php

namespace App\Models;

use App\Models\Traits\BelongsToKindergarten;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory, BelongsToKindergarten;

    protected $fillable = [
        'kindergarten_id',
        'student_id',
        'invoice_number',
        'billing_month',
        'subtotal',
        'discount_total',
        'total_amount',
        'paid_amount',
        'balance_due',
        'status',
        'due_date',
        'sent_at',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'billing_month' => 'date',
            'due_date' => 'date',
            'sent_at' => 'datetime',
            'paid_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('status', ['draft', 'sent', 'partially_paid', 'overdue']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->whereIn('status', ['sent', 'partially_paid'])
                  ->where('due_date', '<', now()->toDateString());
            });
    }

    /**
     * Recalculate invoice balances based on completed payments.
     */
    public function recalculateBalances(): void
    {
        $paid = (float) $this->payments()
            ->where('status', 'completed')
            ->sum('amount');

        $total = (float) $this->total_amount;
        $balance = max(0, $total - $paid);

        $this->paid_amount = $paid;
        $this->balance_due = $balance;

        if ($balance <= 0) {
            $this->status = 'paid';
            $this->paid_at = $this->paid_at ?? now();
        } elseif ($paid > 0) {
            $this->status = 'partially_paid';
        } elseif ($this->due_date < now()->toDateString() && $this->status !== 'draft') {
            $this->status = 'overdue';
        }

        $this->save();
    }
}
