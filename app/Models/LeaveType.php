<?php

namespace App\Models;

use App\Models\Traits\BelongsToKindergarten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory, BelongsToKindergarten;

    protected $fillable = [
        'kindergarten_id',
        'name',
        'code',
        'is_paid',
        'requires_attachment',
        'carry_forward',
        'max_carry_forward',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'requires_attachment' => 'boolean',
        'carry_forward' => 'boolean',
        'max_carry_forward' => 'integer',
    ];

    public function entitlements(): HasMany
    {
        return $this->hasMany(LeaveEntitlement::class);
    }

    public function balances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
