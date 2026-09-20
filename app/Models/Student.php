<?php

namespace App\Models;

use App\Models\Traits\BelongsToKindergarten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory, BelongsToKindergarten;

    protected $fillable = [
        'kindergarten_id',
        'class_group_id',
        'name',
        'ic_number',
        'date_of_birth',
        'gender',
        'photo_path',
        'allergies',
        'medical_notes',
        'enrollment_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'ic_number' => 'encrypted',
            'date_of_birth' => 'date',
            'enrollment_date' => 'date',
        ];
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'student_guardian')
            ->withPivot('relationship');
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
