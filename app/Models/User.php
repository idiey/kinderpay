<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'kindergarten_id', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kindergarten(): BelongsTo
    {
        return $this->belongsTo(Kindergarten::class);
    }

    public function activeKindergarten(): ?Kindergarten
    {
        if ($this->hasRole('super_admin') || $this->role === 'super_admin') {
            $activeId = session('active_kindergarten_id');
            if ($activeId) {
                return Kindergarten::find($activeId) ?? $this->kindergarten ?? Kindergarten::first();
            }
            return $this->kindergarten ?? Kindergarten::first();
        }

        return $this->kindergarten;
    }

    public function activeKindergartenId(): ?int
    {
        return $this->activeKindergarten()?->id ?? $this->kindergarten_id;
    }
}
