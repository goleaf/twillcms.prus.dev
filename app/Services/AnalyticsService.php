<?php

namespace App\Services;

use App\Models\Analytics;
use App\Models\Post;

class AnalyticsService
{
    public static function trackPageView($trackable, array $metadata = []): void
    {
        Analytics::track($trackable, 'view', array_merge($metadata, [
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId(),
        ]));
    }

    public static function trackClick($trackable, string $element, array $metadata = []): void
    {
        Analytics::track($trackable, 'click', array_merge($metadata, [
            'element' => $element,
            'timestamp' => now()->toISOString(),
        ]));
    }

    public static function trackShare($trackable, string $platform, array $metadata = []): void
    {
        Analytics::track($trackable, 'share', array_merge($metadata, [
            'platform' => $platform,
            'timestamp' => now()->toISOString(),
        ]));
    }

    public static function getPostStats(Post $post): array
    {
        $analytics = Analytics::forTrackable($post);

        return [
            'total_views' => $analytics->views()->count(),
            'today_views' => $analytics->views()->today()->count(),
            'week_views' => $analytics->views()->thisWeek()->count(),
            'month_views' => $analytics->views()->thisMonth()->count(),
            'total_shares' => $analytics->shares()->count(),
            'total_clicks' => $analytics->clicks()->count(),
            'daily_views' => $analytics->views()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('views', 'date'),
        ];
    }

    public static function getDashboardStats(): array
    {
        return [
            'total_page_views' => Analytics::views()->count(),
            'today_page_views' => Analytics::views()->today()->count(),
            'unique_visitors_today' => Analytics::today()->distinct('ip_address')->count('ip_address'),
            'popular_posts' => Analytics::getPopularContent(Post::class, 5),
            'trending_posts' => Analytics::getTrendingContent(Post::class, 7, 5),
            'traffic_sources' => Analytics::views()
                ->thisMonth()
                ->selectRaw('
                    CASE
                        WHEN referrer IS NULL OR referrer = "" THEN "Direct"
                        WHEN referrer LIKE "%google.%" THEN "Google"
                        WHEN referrer LIKE "%facebook.%" THEN "Facebook"
                        WHEN referrer LIKE "%twitter.%" THEN "Twitter"
                        ELSE "Other"
                    END as source,
                    COUNT(*) as visits
                ')
                ->groupBy('source')
                ->orderByDesc('visits')
                ->get(),
        ];
    }
}
