<?php

namespace App\Models;

use App\Models\Traits\BelongsToKindergarten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guardian extends Model
{
    use HasFactory, BelongsToKindergarten;

    protected $fillable = [
        'kindergarten_id',
        'user_id',
        'name',
        'ic_number',
        'relationship',
        'phone',
        'email',
        'whatsapp',
        'address',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'ic_number' => 'encrypted',
            'is_primary' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_guardian')
            ->withPivot('relationship');
    }
}
