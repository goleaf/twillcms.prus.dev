<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of all tags
     */
    public function index()
    {
        $tags = Tag::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
        ->orderBy('name')
        ->get();

        return view('news.tags.index', compact('tags'));
    }

    /**
     * Display posts filtered by tag
     */
    public function show(Tag $tag)
    {
        $posts = Post::whereHas('tags', function ($query) use ($tag) {
            $query->where('tags.id', $tag->id);
        })
        ->where('status', 'published')
        ->with(['tags', 'categories'])
        ->latest()
        ->paginate(12);

        // Get related tags (tags that appear with this tag in other posts)
        $relatedTags = Tag::whereHas('posts', function ($query) use ($tag) {
            $query->whereHas('tags', function ($subQuery) use ($tag) {
                $subQuery->where('tags.id', $tag->id);
            });
        })
        ->where('id', '!=', $tag->id)
        ->withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
        ->orderBy('posts_count', 'desc')
        ->take(10)
        ->get();

        return view('news.tags.show', compact('tag', 'posts', 'relatedTags'));
    }
} 