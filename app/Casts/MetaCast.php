<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MetaCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (is_null($value)) {
            return [];
        }

        $decoded = json_decode($value, true);
        
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (is_null($value)) {
            return json_encode([]);
        }

        if (is_string($value)) {
            // Try to decode if it's already JSON
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $value;
            }
        }

        return json_encode($value);
    }
} 