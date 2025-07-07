<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'author_name',
        'author_email',
        'content',
        'status',
        'ip_address',
        'user_agent',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    protected $appends = [
        'is_approved',
        'author_display_name',
        'replies_count',
    ];

    // ====================
    // RELATIONSHIPS
    // ====================

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // ====================
    // SCOPES
    // ====================

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    // ====================
    // ACCESSORS
    // ====================

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getAuthorDisplayNameAttribute(): string
    {
        return $this->user ? $this->user->name : $this->author_name;
    }

    public function getRepliesCountAttribute(): int
    {
        return $this->replies()->count();
    }

    // ====================
    // METHODS
    // ====================

    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }
}
