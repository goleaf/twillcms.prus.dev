<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('categories');

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            $query->where('published', $request->status === 'published');
        }

        if ($request->category) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::all();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:500',
            'content' => 'required',
            'published' => 'boolean',
            'published_at' => 'nullable|date',
            'excerpt_override' => 'nullable|max:300',
            'priority' => 'nullable|integer|min:0|max:100',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $post = new Post();
        $post->title = $validated['title'];
        $post->description = $validated['description'] ?? '';
        $post->content = $validated['content'];
        $post->slug = Str::slug($validated['title']);
        $post->published = $validated['published'] ?? false;
        $post->published_at = $validated['published_at'] ? now()->parse($validated['published_at']) : null;
        $post->excerpt_override = $validated['excerpt_override'] ?? null;
        $post->priority = $validated['priority'] ?? 0;
        
        // Handle meta data
        $post->meta = [
            'description' => $request->input('meta_description'),
            'keywords' => $request->input('meta_keywords'),
        ];

        // Handle settings
        $post->settings = [
            'is_featured' => $request->boolean('is_featured'),
            'is_trending' => $request->boolean('is_trending'),
            'is_breaking' => $request->boolean('is_breaking'),
        ];

        $post->save();

        // Attach categories
        if (!empty($validated['categories'])) {
            $post->categories()->attach($validated['categories']);
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        $post->load('categories');
        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $post->load('categories');
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:500',
            'content' => 'required',
            'published' => 'boolean',
            'published_at' => 'nullable|date',
            'excerpt_override' => 'nullable|max:300',
            'priority' => 'nullable|integer|min:0|max:100',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $post->title = $validated['title'];
        $post->description = $validated['description'] ?? '';
        $post->content = $validated['content'];
        $post->slug = Str::slug($validated['title']);
        $post->published = $validated['published'] ?? false;
        $post->published_at = $validated['published_at'] ? now()->parse($validated['published_at']) : null;
        $post->excerpt_override = $validated['excerpt_override'] ?? null;
        $post->priority = $validated['priority'] ?? 0;
        
        // Handle meta data
        $post->meta = [
            'description' => $request->input('meta_description'),
            'keywords' => $request->input('meta_keywords'),
        ];

        // Handle settings
        $post->settings = [
            'is_featured' => $request->boolean('is_featured'),
            'is_trending' => $request->boolean('is_trending'),
            'is_breaking' => $request->boolean('is_breaking'),
        ];

        $post->save();

        // Sync categories
        $post->categories()->sync($validated['categories'] ?? []);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $post->categories()->detach();
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully!');
    }
}
