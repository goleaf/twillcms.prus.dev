<?php

namespace App\Models\Slugs;

use A17\Twill\Models\Model;

class CategorySlug extends Model
{
    protected $table = 'category_slugs';

    protected $fillable = [
        'category_id',
        'slug',
        'locale',
        'active',
    ];
}
