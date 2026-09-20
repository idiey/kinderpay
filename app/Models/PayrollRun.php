<?php

namespace App\Models;

use App\Models\Traits\BelongsToKindergarten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    use HasFactory, BelongsToKindergarten;

    protected $fillable = [
        'kindergarten_id',
        'month',
        'year',
        'status',
        'total_gross',
        'total_deductions',
        'total_net',
        'total_employer_cost',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_gross' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net' => 'decimal:2',
        'total_employer_cost' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function recalculateTotals(): void
    {
        $this->total_gross = (float) $this->items()->sum('gross_pay');
        $this->total_deductions = (float) $this->items()->sum('total_deductions');
        $this->total_net = (float) $this->items()->sum('net_pay');
        
        $employerCost = (float) $this->items()->sum('gross_pay') 
            + (float) $this->items()->sum('epf_employer')
            + (float) $this->items()->sum('socso_employer')
            + (float) $this->items()->sum('eis_employer');
            
        $this->total_employer_cost = $employerCost;
        $this->save();
    }
}
