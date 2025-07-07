<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_filename',
        'path',
        'mime_type',
        'size',
        'alt_text',
        'caption',
        'metadata',
        'user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
    ];

    protected $appends = [
        'url',
        'human_readable_size',
        'is_image',
    ];

    // ====================
    // RELATIONSHIPS
    // ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ====================
    // SCOPES
    // ====================

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    public function scopeDocuments($query)
    {
        return $query->whereNotIn('mime_type', ['image/%', 'video/%']);
    }

    // ====================
    // ACCESSORS
    // ====================

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getHumanReadableSizeAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    // ====================
    // METHODS
    // ====================

    public function delete(): bool
    {
        // Delete the actual file
        if (Storage::exists($this->path)) {
            Storage::delete($this->path);
        }

        return parent::delete();
    }

    public function getThumbnailUrl(int $width = 300, int $height = 300): string
    {
        if (!$this->is_image) {
            return $this->url;
        }

        // You can implement image resizing logic here
        // For now, return the original URL
        return $this->url;
    }
}
