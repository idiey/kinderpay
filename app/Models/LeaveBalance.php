<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'leave_type_id',
        'year',
        'entitled',
        'carried_forward',
        'used',
        'pending',
        'balance',
    ];

    protected $casts = [
        'entitled' => 'decimal:1',
        'carried_forward' => 'decimal:1',
        'used' => 'decimal:1',
        'pending' => 'decimal:1',
        'balance' => 'decimal:1',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function recalculate(): void
    {
        $this->balance = ($this->entitled + $this->carried_forward) - $this->used;
        $this->save();
    }
}
