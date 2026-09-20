<?php

namespace App\Models\Traits;

use App\Models\Kindergarten;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToKindergarten
{
    protected static function bootBelongsToKindergarten(): void
    {
        static::addGlobalScope('kindergarten', function (Builder $builder) {
            if (Auth::check() && Auth::user()->kindergarten_id) {
                $builder->where('kindergarten_id', Auth::user()->kindergarten_id);
            }
        });

        static::creating(function ($model) {
            if (empty($model->kindergarten_id) && Auth::check() && Auth::user()->kindergarten_id) {
                $model->kindergarten_id = Auth::user()->kindergarten_id;
            }
        });
    }

    public function kindergarten(): BelongsTo
    {
        return $this->belongsTo(Kindergarten::class);
    }
}
