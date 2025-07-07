<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'trackable_type',
        'trackable_id',
        'event_type',
        'ip_address',
        'user_agent',
        'referrer',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    // ====================
    // RELATIONSHIPS
    // ====================
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'trackable_type',
        'trackable_id',
        'event_type',
        'ip_address',
        'user_agent',
        'referrer',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false; // Only using created_at

    // ====================
    // RELATIONSHIPS
    // ====================
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $fillable = [
        'trackable_type',
        'trackable_id',
        'event_type',
        'ip_address',
        'user_agent',
        'referrer',
        'metadata',
        'created_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime'
    ];

    public $timestamps = false;

    /**
     * Get the trackable model.
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    /**
     * Scope for filtering by event type.
     */
    public function scopeEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for filtering by trackable type.
     */
    public function scopeTrackableType($query, $type)
    {
        return $query->where('trackable_type', $type);
    }

    /**
     * Get analytics for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    // ====================
    // SCOPES
    // ====================

    public function scopeForEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeForTrackable($query, $trackable)
    {
        return $query->where('trackable_type', get_class($trackable))
                    ->where('trackable_id', $trackable->id);
    }

    public function scopeViews($query)
    {
        return $query->where('event_type', 'view');
    }

    public function scopeClicks($query)
    {
        return $query->where('event_type', 'click');
    }

    public function scopeShares($query)
    {
        return $query->where('event_type', 'share');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // ====================
    // STATIC METHODS
    // ====================

    public static function track($trackable, string $eventType, array $metadata = []): void
    {
        static::create([
            'trackable_type' => get_class($trackable),
            'trackable_id' => $trackable->id,
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->headers->get('referer'),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public static function getPopularContent(string $trackableType, int $limit = 10)
    {
        return static::where('trackable_type', $trackableType)
            ->where('event_type', 'view')
            ->selectRaw('trackable_id, COUNT(*) as views')
            ->groupBy('trackable_id')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    public static function getTrendingContent(string $trackableType, int $days = 7, int $limit = 10)
    {
        return static::where('trackable_type', $trackableType)
            ->where('event_type', 'view')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('trackable_id, COUNT(*) as views')
            ->groupBy('trackable_id')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }
}
    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    // ====================
    // SCOPES
    // ====================

    public function scopeForModel($query, $model)
    {
        return $query->where('trackable_type', get_class($model))
                    ->where('trackable_id', $model->id);
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeViews($query)
    {
        return $query->where('event_type', 'view');
    }

    public function scopeClicks($query)
    {
        return $query->where('event_type', 'click');
    }

    public function scopeShares($query)
    {
        return $query->where('event_type', 'share');
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ====================
    // STATIC METHODS
    // ====================

    public static function track($trackable, string $eventType, array $metadata = []): void
    {
        static::create([
            'trackable_type' => get_class($trackable),
            'trackable_id' => $trackable->id,
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('Referer'),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public static function getPopularContent(string $modelType, int $days = 30, int $limit = 10)
    {
        return static::where('trackable_type', $modelType)
            ->where('event_type', 'view')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('trackable_id, COUNT(*) as views')
            ->groupBy('trackable_id')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    public static function getTrendingPosts(int $days = 7, int $limit = 5)
    {
        return static::where('trackable_type', Post::class)
            ->where('event_type', 'view')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('trackable_id, COUNT(*) as views')
            ->groupBy('trackable_id')
            ->orderByDesc('views')
            ->limit($limit)
            ->with('trackable')
            ->get();
    }
}
