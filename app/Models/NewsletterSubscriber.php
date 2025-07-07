<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'status',
        'subscribed_at',
        'unsubscribed_at',
        'verification_token',
        'verified_at',
        'preferences',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'verified_at' => 'datetime',
        'preferences' => 'array',
    ];

    protected $appends = [
        'is_verified',
        'is_active',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (empty($subscriber->verification_token)) {
                $subscriber->verification_token = Str::random(64);
            }
            if (empty($subscriber->subscribed_at)) {
                $subscriber->subscribed_at = now();
            }
        });
    }

    // ====================
    // SCOPES
    // ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    // ====================
    // ACCESSORS
    // ====================

    public function getIsVerifiedAttribute(): bool
    {
        return !is_null($this->verified_at);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    // ====================
    // METHODS
    // ====================

    public function verify(): void
    {
        $this->update([
            'verified_at' => now(),
            'verification_token' => null,
            'status' => 'active',
        ]);
    }

    public function unsubscribe(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    public function resubscribe(): void
    {
        $this->update([
            'status' => 'active',
            'unsubscribed_at' => null,
        ]);
    }

    public function updatePreferences(array $preferences): void
    {
        $this->update(['preferences' => $preferences]);
    }

    public function hasPreference(string $key): bool
    {
        return isset($this->preferences[$key]) && $this->preferences[$key];
    }
}
