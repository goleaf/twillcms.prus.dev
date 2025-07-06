<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class SettingsCast implements CastsAttributes
{
    protected array $defaults = [
        'is_featured' => false,
        'is_trending' => false,
        'is_breaking' => false,
        'comment_enabled' => true,
        'share_enabled' => true,
        'seo_optimized' => false,
        'reading_time_override' => null,
        'priority' => 0,
        'external_url' => null,
        'author_override' => null,
    ];

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (is_null($value)) {
            return $this->defaults;
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            return $this->defaults;
        }

        // Merge with defaults to ensure all keys exist
        return array_merge($this->defaults, $decoded);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (is_null($value)) {
            return json_encode($this->defaults);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (! is_array($value)) {
            return json_encode($this->defaults);
        }

        // Merge with defaults and filter valid keys
        $settings = array_merge($this->defaults, array_intersect_key($value, $this->defaults));

        return json_encode($settings);
    }
}
