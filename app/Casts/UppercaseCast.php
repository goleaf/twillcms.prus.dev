<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class UppercaseCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return ucwords($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return strtolower($value);
    }
}
