<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('news.categories.index', compact('categories'));
    }

    /**
     * Display the specified category and its posts
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $posts = Post::where('status', 'published')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->with(['categories', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('news.categories.show', compact('category', 'posts'));
    }
} 