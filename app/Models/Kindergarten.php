<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kindergarten extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'registration_no',
        'address',
        'city',
        'state',
        'postcode',
        'phone',
        'email',
        'logo_path',
        'invoice_prefix',
        'invoice_day',
        'payment_gateway',
        'gateway_api_key',
        'gateway_collection_id',
        'timezone',
    ];

    protected $casts = [
        'gateway_api_key' => 'encrypted',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(\App\Models\Student::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(\App\Models\Staff::class);
    }

    public function classGroups(): HasMany
    {
        return $this->hasMany(\App\Models\ClassGroup::class);
    }

    public function feeTemplates(): HasMany
    {
        return $this->hasMany(\App\Models\FeeTemplate::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Payment::class);
    }
}
