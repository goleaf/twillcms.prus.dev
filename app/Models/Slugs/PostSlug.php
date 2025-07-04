<?php

namespace App\Models\Slugs;

use A17\Twill\Models\Model;

class PostSlug extends Model
{
    protected $table = 'post_slugs';

    protected $fillable = [
        'post_id',
        'slug',
        'locale',
        'active',
    ];
}
