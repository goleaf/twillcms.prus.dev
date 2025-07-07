<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title ?? $model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') || $model->isDirty('name')) {
                $model->slug = Str::slug($model->title ?? $model->name);
            }
        });
    }

    public function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = static::where('slug', $slug)->count();

        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        return $slug;
    }
}
