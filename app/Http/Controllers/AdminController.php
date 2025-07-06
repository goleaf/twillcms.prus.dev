<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Debug: Log that we're in the admin controller
        Log::info('AdminController dashboard method called');

        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::where('published', true)->count(),
            'total_categories' => Category::count(),
            'total_views' => Post::sum('view_count'),
        ];

        $recent_posts = Post::latest()->limit(5)->get();
        $popular_posts = Post::orderBy('view_count', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_posts', 'popular_posts'));
    }
}
