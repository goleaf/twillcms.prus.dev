<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTranslation extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'locale',
        'active',
        'title',
        'description',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the category that owns the translation.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
