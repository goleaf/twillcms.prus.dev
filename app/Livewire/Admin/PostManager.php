<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use App\Models\Category;
use App\Repositories\Eloquent\PostRepository;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;

class PostManager extends Component
{
    use WithPagination;

    protected $postRepository;

    // Search and filter properties
    public $search = '';
    public $statusFilter = 'all';
    public $categoryFilter = 'all';
    public $sortBy = 'published_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal properties
    public $showModal = false;
    public $editingPost = null;
    public $modalTitle = 'Create Post';

    // Form properties
    #[Rule('required|min:3')]
    public $title = '';
    
    #[Rule('required|min:10')]
    public $description = '';
    
    #[Rule('required|min:50')]
    public $content = '';
    
    #[Rule('boolean')]
    public $published = false;
    
    #[Rule('nullable|date')]
    public $published_at = '';
    
    #[Rule('array')]
    public $selectedCategories = [];
    
    #[Rule('integer|min:0')]
    public $priority = 0;
    
    #[Rule('integer|min:0')]
    public $view_count = 0;

    // Settings properties
    public $isFeatured = false;
    public $isTrending = false;
    public $isBreaking = false;
    public $commentEnabled = true;
    public $shareEnabled = true;
    public $seoOptimized = false;
    public $readingTimeOverride = null;
    public $externalUrl = null;
    public $authorOverride = null;

    // Meta properties
    public $metaDescription = '';
    public $metaKeywords = '';

    public function boot(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function mount()
    {
        $this->published_at = now()->format('Y-m-d H:i');
    }

    #[Computed]
    public function posts()
    {
        $query = Post::with(['categories', 'author']);

        // Apply search
        if ($this->search) {
            $query->search($this->search);
        }

        // Apply status filter
        if ($this->statusFilter === 'published') {
            $query->published();
        } elseif ($this->statusFilter === 'draft') {
            $query->where('published', false);
        } elseif ($this->statusFilter === 'featured') {
            $query->featured();
        } elseif ($this->statusFilter === 'trending') {
            $query->trending();
        }

        // Apply category filter
        if ($this->categoryFilter !== 'all') {
            $query->whereHas('categories', function($q) {
                $q->where('categories.id', $this->categoryFilter);
            });
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function categories()
    {
        return Category::published()->orderBy('title')->get();
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Post::count(),
            'published' => Post::published()->count(),
            'draft' => Post::where('published', false)->count(),
            'featured' => Post::featured()->count(),
            'trending' => Post::trending()->count(),
            'total_views' => Post::sum('view_count'),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->modalTitle = 'Create Post';
        $this->editingPost = null;
        $this->showModal = true;
    }

    public function edit($postId)
    {
        $post = Post::with('categories')->findOrFail($postId);
        $this->editingPost = $post;
        $this->modalTitle = 'Edit Post';
        
        // Populate form
        $this->title = $post->title;
        $this->description = $post->description;
        $this->content = $post->content;
        $this->published = $post->published;
        $this->published_at = $post->published_at?->format('Y-m-d H:i');
        $this->selectedCategories = $post->categories->pluck('id')->toArray();
        $this->priority = $post->priority;
        $this->view_count = $post->view_count;

        // Settings
        $this->isFeatured = $post->settings['is_featured'] ?? false;
        $this->isTrending = $post->settings['is_trending'] ?? false;
        $this->isBreaking = $post->settings['is_breaking'] ?? false;
        $this->commentEnabled = $post->settings['comment_enabled'] ?? true;
        $this->shareEnabled = $post->settings['share_enabled'] ?? true;
        $this->seoOptimized = $post->settings['seo_optimized'] ?? false;
        $this->readingTimeOverride = $post->settings['reading_time_override'];
        $this->externalUrl = $post->settings['external_url'];
        $this->authorOverride = $post->settings['author_override'];

        // Meta
        $this->metaDescription = $post->meta['description'] ?? '';
        $this->metaKeywords = $post->meta['keywords'] ?? '';

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'published' => $this->published,
            'published_at' => $this->published_at ? \Carbon\Carbon::parse($this->published_at) : null,
            'priority' => $this->priority,
            'view_count' => $this->view_count,
            'settings' => [
                'is_featured' => $this->isFeatured,
                'is_trending' => $this->isTrending,
                'is_breaking' => $this->isBreaking,
                'comment_enabled' => $this->commentEnabled,
                'share_enabled' => $this->shareEnabled,
                'seo_optimized' => $this->seoOptimized,
                'reading_time_override' => $this->readingTimeOverride,
                'external_url' => $this->externalUrl,
                'author_override' => $this->authorOverride,
            ],
            'meta' => [
                'description' => $this->metaDescription,
                'keywords' => $this->metaKeywords,
            ]
        ];

        if ($this->editingPost) {
            $this->editingPost->update($data);
            $post = $this->editingPost;
            session()->flash('message', 'Post updated successfully!');
        } else {
            $post = Post::create($data);
            session()->flash('message', 'Post created successfully!');
        }

        // Sync categories
        $post->categories()->sync($this->selectedCategories);

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($postId)
    {
        $post = Post::findOrFail($postId);
        $post->delete();
        
        session()->flash('message', 'Post deleted successfully!');
    }

    public function duplicate($postId)
    {
        $originalPost = Post::with('categories')->findOrFail($postId);
        
        $newPost = $originalPost->replicate();
        $newPost->title = $originalPost->title . ' (Copy)';
        $newPost->published = false;
        $newPost->published_at = null;
        $newPost->view_count = 0;
        $newPost->save();

        // Sync categories
        $newPost->categories()->sync($originalPost->categories->pluck('id'));

        session()->flash('message', 'Post duplicated successfully!');
    }

    public function toggleFeatured($postId)
    {
        $post = Post::findOrFail($postId);
        $post->markAsFeatured(!$post->is_featured);
        
        session()->flash('message', 'Post featured status updated!');
    }

    public function toggleTrending($postId)
    {
        $post = Post::findOrFail($postId);
        $post->markAsTrending(!$post->is_trending);
        
        session()->flash('message', 'Post trending status updated!');
    }

    public function togglePublished($postId)
    {
        $post = Post::findOrFail($postId);
        $post->update([
            'published' => !$post->published,
            'published_at' => !$post->published ? now() : null
        ]);
        
        session()->flash('message', 'Post publication status updated!');
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->content = '';
        $this->published = false;
        $this->published_at = now()->format('Y-m-d H:i');
        $this->selectedCategories = [];
        $this->priority = 0;
        $this->view_count = 0;

        // Reset settings
        $this->isFeatured = false;
        $this->isTrending = false;
        $this->isBreaking = false;
        $this->commentEnabled = true;
        $this->shareEnabled = true;
        $this->seoOptimized = false;
        $this->readingTimeOverride = null;
        $this->externalUrl = null;
        $this->authorOverride = null;

        // Reset meta
        $this->metaDescription = '';
        $this->metaKeywords = '';

        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.admin.post-manager');
    }
}
