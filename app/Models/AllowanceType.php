<?php

namespace App\Models;

use App\Models\Traits\BelongsToKindergarten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllowanceType extends Model
{
    use HasFactory, BelongsToKindergarten;

    protected $fillable = [
        'kindergarten_id',
        'name',
        'is_statutory',
    ];

    protected $casts = [
        'is_statutory' => 'boolean',
    ];

    public function staffAllowances(): HasMany
    {
        return $this->hasMany(StaffAllowance::class);
    }
}
