<?php

namespace App\Models;

use App\Models\Traits\BelongsToKindergarten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    use HasFactory, BelongsToKindergarten;

    protected $table = 'staff';

    protected $fillable = [
        'kindergarten_id',
        'user_id',
        'employee_id',
        'name',
        'ic_number',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'position',
        'employment_type',
        'join_date',
        'end_date',
        'status',
        'basic_salary',
        'bank_name',
        'bank_account',
        'epf_number',
        'socso_number',
        'tax_number',
        'epf_category',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'ic_number' => 'encrypted',
            'bank_name' => 'encrypted',
            'bank_account' => 'encrypted',
            'epf_number' => 'encrypted',
            'socso_number' => 'encrypted',
            'tax_number' => 'encrypted',
            'basic_salary' => 'decimal:2',
            'date_of_birth' => 'date',
            'join_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function allowances(): HasMany
    {
        return $this->hasMany(StaffAllowance::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }
}
