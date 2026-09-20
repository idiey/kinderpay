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
            if (Auth::check()) {
                $user = Auth::user();
                $tenantId = method_exists($user, 'activeKindergartenId') 
                    ? $user->activeKindergartenId() 
                    : $user->kindergarten_id;

                if ($tenantId) {
                    $builder->where($builder->getModel()->getTable() . '.kindergarten_id', $tenantId);
                }
            }
        });

        static::creating(function ($model) {
            if (empty($model->kindergarten_id) && Auth::check()) {
                $user = Auth::user();
                $tenantId = method_exists($user, 'activeKindergartenId') 
                    ? $user->activeKindergartenId() 
                    : $user->kindergarten_id;

                if ($tenantId) {
                    $model->kindergarten_id = $tenantId;
                }
            }
        });
    }

    public function kindergarten(): BelongsTo
    {
        return $this->belongsTo(Kindergarten::class);
    }
}
