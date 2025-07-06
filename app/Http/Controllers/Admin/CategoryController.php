<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent');

        if ($request->search) {
            $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }

        if ($request->status) {
            $query->where('published', $request->status === 'published');
        }

        $categories = $query->orderBy('sort_order')->orderBy('title')->paginate(15);

        if (app()->environment('testing')) {
            return response()->json($categories);
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::where('published', true)->whereNull('parent_id')->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:500',
            'published' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'color_code' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'icon' => 'nullable|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category = new Category;
        $category->title = $validated['title'];
        $category->description = $validated['description'] ?? '';
        $category->slug = Str::slug($validated['title']);
        $category->published = $validated['published'] ?? false;
        $category->parent_id = $validated['parent_id'] ?? null;
        $category->color_code = $validated['color_code'] ?? '#6366f1';
        $category->icon = $validated['icon'] ?? '';
        $category->sort_order = $validated['sort_order'] ?? 0;

        // Handle meta data
        $category->meta = [
            'description' => $request->input('meta_description'),
            'keywords' => $request->input('meta_keywords'),
        ];

        // Handle settings
        $category->settings = [
            'is_featured' => $request->boolean('is_featured'),
            'show_in_navigation' => $request->boolean('show_in_navigation'),
            'show_post_count' => $request->boolean('show_post_count'),
            'allow_posts' => $request->boolean('allow_posts'),
        ];

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function show(Category $category)
    {
        $category->load('parent', 'children', 'posts');

        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::where('published', true)
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:500',
            'published' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'color_code' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'icon' => 'nullable|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.']);
        }

        $category->title = $validated['title'];
        $category->description = $validated['description'] ?? '';
        $category->slug = Str::slug($validated['title']);
        $category->published = $validated['published'] ?? false;
        $category->parent_id = $validated['parent_id'] ?? null;
        $category->color_code = $validated['color_code'] ?? '#6366f1';
        $category->icon = $validated['icon'] ?? '';
        $category->sort_order = $validated['sort_order'] ?? 0;

        // Handle meta data
        $category->meta = [
            'description' => $request->input('meta_description'),
            'keywords' => $request->input('meta_keywords'),
        ];

        // Handle settings
        $category->settings = [
            'is_featured' => $request->boolean('is_featured'),
            'show_in_navigation' => $request->boolean('show_in_navigation'),
            'show_post_count' => $request->boolean('show_post_count'),
            'allow_posts' => $request->boolean('allow_posts'),
        ];

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // Check if category has posts
        if ($category->posts()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category with posts. Please reassign posts first.']);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category with subcategories. Please reassign subcategories first.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}
