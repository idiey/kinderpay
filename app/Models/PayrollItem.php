<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id',
        'staff_id',
        'basic_salary',
        'total_allowances',
        'overtime_amount',
        'bonus_amount',
        'gross_pay',
        'unpaid_leave_days',
        'unpaid_leave_deduction',
        'adjusted_gross',
        'epf_employee',
        'epf_employer',
        'socso_employee',
        'socso_employer',
        'eis_employee',
        'eis_employer',
        'pcb_amount',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'notes',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'unpaid_leave_days' => 'decimal:1',
        'unpaid_leave_deduction' => 'decimal:2',
        'adjusted_gross' => 'decimal:2',
        'epf_employee' => 'decimal:2',
        'epf_employer' => 'decimal:2',
        'socso_employee' => 'decimal:2',
        'socso_employer' => 'decimal:2',
        'eis_employee' => 'decimal:2',
        'eis_employer' => 'decimal:2',
        'pcb_amount' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function allowanceItems(): HasMany
    {
        return $this->hasMany(PayrollAllowanceItem::class);
    }
}
