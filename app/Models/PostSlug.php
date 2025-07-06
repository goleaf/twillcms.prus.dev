<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSlug extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'locale',
        'slug',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
