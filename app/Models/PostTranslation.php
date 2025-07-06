<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'locale',
        'active',
        'title',
        'description',
        'content',
        'excerpt_override',
        'featured_image_caption',
    ];

    /**
     * Get the post that owns the translation.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
