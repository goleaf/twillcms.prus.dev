<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Analytics extends Component
{
    // Filter properties
    public $dateRange = '30';
    public $selectedCategory = 'all';
    public $selectedAuthor = 'all';
    public $metricType = 'views';

    // Real-time refresh
    public $autoRefresh = true;
    public $lastUpdated;

    public function mount()
    {
        $this->lastUpdated = now()->format('Y-m-d H:i:s');
    }

    #[Computed]
    public function overviewStats()
    {
        $dateFrom = Carbon::now()->subDays($this->dateRange);
        
        return [
            'total_posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'draft_posts' => Post::where('published', false)->count(),
            'total_categories' => Category::count(),
            'total_views' => Post::sum('view_count'),
            'period_views' => Post::where('created_at', '>=', $dateFrom)->sum('view_count'),
            'featured_posts' => Post::featured()->count(),
            'trending_posts' => Post::trending()->count(),
            'breaking_posts' => Post::breaking()->count(),
        ];
    }

    #[Computed]
    public function topPosts()
    {
        $dateFrom = Carbon::now()->subDays($this->dateRange);
        
        return Post::published()
            ->with(['categories', 'author'])
            ->when($this->selectedCategory !== 'all', function($query) {
                $query->whereHas('categories', function($q) {
                    $q->where('categories.id', $this->selectedCategory);
                });
            })
            ->when($this->selectedAuthor !== 'all', function($query) {
                $query->where('author_id', $this->selectedAuthor);
            })
            ->where('created_at', '>=', $dateFrom)
            ->orderBy($this->metricType === 'views' ? 'view_count' : 'created_at', 'desc')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function topCategories()
    {
        $dateFrom = Carbon::now()->subDays($this->dateRange);
        
        return Category::published()
            ->withCount(['posts' => function($query) use ($dateFrom) {
                $query->published()->where('created_at', '>=', $dateFrom);
            }])
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function dailyStats()
    {
        $days = min($this->dateRange, 30); // Limit to 30 days for chart
        $dates = collect();
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates->push($date);
        }
        
        $dailyPosts = Post::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as posts_count'),
                DB::raw('SUM(view_count) as total_views')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        
        return $dates->map(function($date) use ($dailyPosts) {
            $stats = $dailyPosts->get($date);
            return [
                'date' => $date,
                'formatted_date' => Carbon::parse($date)->format('M j'),
                'posts' => $stats ? $stats->posts_count : 0,
                'views' => $stats ? $stats->total_views : 0,
            ];
        });
    }

    #[Computed]
    public function categoryDistribution()
    {
        return Category::published()
            ->withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->orderBy('published_posts_count', 'desc')
            ->limit(8)
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->title,
                    'count' => $category->published_posts_count,
                    'color' => $category->color_code ?? '#6366f1',
                    'percentage' => 0, // Will be calculated in the view
                ];
            });
    }

    #[Computed]
    public function performanceMetrics()
    {
        $dateFrom = Carbon::now()->subDays($this->dateRange);
        $previousPeriodFrom = Carbon::now()->subDays($this->dateRange * 2);
        
        // Current period stats
        $currentPosts = Post::where('created_at', '>=', $dateFrom)->count();
        $currentViews = Post::where('created_at', '>=', $dateFrom)->sum('view_count');
        
        // Previous period stats
        $previousPosts = Post::where('created_at', '>=', $previousPeriodFrom)
            ->where('created_at', '<', $dateFrom)
            ->count();
        $previousViews = Post::where('created_at', '>=', $previousPeriodFrom)
            ->where('created_at', '<', $dateFrom)
            ->sum('view_count');
        
        return [
            'posts_growth' => $previousPosts > 0 ? (($currentPosts - $previousPosts) / $previousPosts) * 100 : 0,
            'views_growth' => $previousViews > 0 ? (($currentViews - $previousViews) / $previousViews) * 100 : 0,
            'avg_views_per_post' => $currentPosts > 0 ? round($currentViews / $currentPosts, 1) : 0,
        ];
    }

    #[Computed]
    public function availableCategories()
    {
        return Category::published()
            ->orderBy('title')
            ->get();
    }

    #[Computed]
    public function availableAuthors()
    {
        return DB::table('users')
            ->join('posts', 'users.id', '=', 'posts.author_id')
            ->select('users.id', 'users.name')
            ->distinct()
            ->orderBy('users.name')
            ->get();
    }

    public function refreshData()
    {
        $this->lastUpdated = now()->format('Y-m-d H:i:s');
        $this->dispatch('dataRefreshed');
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function updateDateRange($range)
    {
        $this->dateRange = $range;
        $this->refreshData();
    }

    public function exportData($type)
    {
        // Export functionality could be implemented here
        session()->flash('message', "Export feature for {$type} will be available soon.");
    }

    public function render()
    {
        return view('livewire.admin.analytics');
    }
}
