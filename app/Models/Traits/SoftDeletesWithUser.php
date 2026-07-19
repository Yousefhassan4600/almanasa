<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait SoftDeletesWithUser
{
    public static function bootSoftDeletesWithUser(): void
    {
        static::deleting(function ($model): void {
            if ($model->isForceDeleting()) {
                return;
            }

            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }

            foreach ($model->getSoftDeleteRelations() as $relation) {
                if (method_exists($model, $relation)) {
                    $model->{$relation}()->get()->each->delete();
                }
            }
        });

        static::restoring(function ($model): void {
            $usesTimestamps = $model->timestamps;

            $model->deleted_by = null;
            $model->timestamps = false;
            $model->saveQuietly();
            $model->timestamps = $usesTimestamps;

            foreach ($model->getSoftDeleteRelations() as $relation) {
                if (method_exists($model, $relation)) {
                    $model->{$relation}()->withTrashed()->get()->each->restore();
                }
            }
        });
    }

    /**
     * @return array<int, string>
     */
    protected function getSoftDeleteRelations(): array
    {
        return [];
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
