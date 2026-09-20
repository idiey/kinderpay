<?php

namespace App\Models;

use App\Models\Traits\BelongsToKindergarten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutoryRate extends Model
{
    use HasFactory, BelongsToKindergarten;

    protected $fillable = [
        'kindergarten_id',
        'type',
        'wage_from',
        'wage_to',
        'rate_type',
        'rate_value',
        'category',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'wage_from' => 'decimal:2',
        'wage_to' => 'decimal:2',
        'rate_value' => 'decimal:4',
        'category' => 'integer',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];
}
