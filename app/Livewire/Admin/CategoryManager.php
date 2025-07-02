<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;

class CategoryManager extends Component
{
    use WithPagination;

    // Search and filter properties
    public $search = '';
    public $statusFilter = 'all';
    public $parentFilter = 'all';
    public $sortBy = 'sort_order';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Modal properties
    public $showModal = false;
    public $editingCategory = null;
    public $modalTitle = 'Create Category';

    // Form properties
    #[Rule('required|min:2')]
    public $title = '';
    
    #[Rule('nullable|min:10')]
    public $description = '';
    
    #[Rule('boolean')]
    public $published = true;
    
    #[Rule('nullable|exists:categories,id')]
    public $parent_id = null;
    
    #[Rule('nullable|regex:/^#[0-9A-Fa-f]{6}$/')]
    public $color_code = '#6366f1';
    
    #[Rule('nullable|string')]
    public $icon = '';
    
    #[Rule('integer|min:0')]
    public $sort_order = 0;
    
    #[Rule('integer|min:0')]
    public $view_count = 0;

    // Settings properties
    public $isFeatured = false;
    public $showInNavigation = true;
    public $showPostCount = true;
    public $allowPosts = true;

    // Meta properties
    public $metaDescription = '';
    public $metaKeywords = '';

    #[Computed]
    public function categories()
    {
        $query = Category::with(['parent', 'children']);

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter === 'published') {
            $query->published();
        } elseif ($this->statusFilter === 'draft') {
            $query->where('published', false);
        } elseif ($this->statusFilter === 'featured') {
            $query->featured();
        }

        // Apply parent filter
        if ($this->parentFilter === 'root') {
            $query->root();
        } elseif ($this->parentFilter === 'children') {
            $query->children();
        } elseif ($this->parentFilter !== 'all') {
            $query->where('parent_id', $this->parentFilter);
        }

        // Apply sorting
        if ($this->sortBy === 'hierarchical') {
            $query->hierarchical();
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    #[Computed]
    public function parentCategories()
    {
        return Category::published()
            ->root()
            ->orderBy('title')
            ->get();
    }

    #[Computed]
    public function allCategories()
    {
        return Category::published()
            ->orderBy('title')
            ->get();
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Category::count(),
            'published' => Category::published()->count(),
            'draft' => Category::where('published', false)->count(),
            'root' => Category::root()->count(),
            'children' => Category::children()->count(),
            'featured' => Category::featured()->count(),
            'with_posts' => Category::withPosts()->count(),
            'total_views' => Category::sum('view_count'),
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

    public function updatedParentFilter()
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
        $this->modalTitle = 'Create Category';
        $this->editingCategory = null;
        $this->showModal = true;
    }

    public function edit($categoryId)
    {
        $category = Category::with('parent')->findOrFail($categoryId);
        $this->editingCategory = $category;
        $this->modalTitle = 'Edit Category';
        
        // Populate form
        $this->title = $category->title;
        $this->description = $category->description ?? '';
        $this->published = $category->published;
        $this->parent_id = $category->parent_id;
        $this->color_code = $category->color_code ?? '#6366f1';
        $this->icon = $category->icon ?? '';
        $this->sort_order = $category->sort_order;
        $this->view_count = $category->view_count;

        // Settings
        $this->isFeatured = $category->settings['is_featured'] ?? false;
        $this->showInNavigation = $category->settings['show_in_navigation'] ?? true;
        $this->showPostCount = $category->settings['show_post_count'] ?? true;
        $this->allowPosts = $category->settings['allow_posts'] ?? true;

        // Meta
        $this->metaDescription = $category->meta['description'] ?? '';
        $this->metaKeywords = $category->meta['keywords'] ?? '';

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // Prepare data
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'published' => $this->published,
            'parent_id' => $this->parent_id ?: null,
            'color_code' => $this->color_code,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'view_count' => $this->view_count,
            'settings' => [
                'is_featured' => $this->isFeatured,
                'show_in_navigation' => $this->showInNavigation,
                'show_post_count' => $this->showPostCount,
                'allow_posts' => $this->allowPosts,
            ],
            'meta' => [
                'description' => $this->metaDescription,
                'keywords' => $this->metaKeywords,
            ]
        ];

        if ($this->editingCategory) {
            // Update existing category
            $this->editingCategory->update($data);
            session()->flash('message', 'Category updated successfully!');
        } else {
            // Create new category
            Category::create($data);
            session()->flash('message', 'Category created successfully!');
        }

        $this->resetForm();
        $this->showModal = false;
        $this->resetPage();
    }

    public function delete($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        // Check if category has children or posts
        if ($category->children()->count() > 0) {
            session()->flash('error', 'Cannot delete category with child categories!');
            return;
        }
        
        if ($category->posts()->count() > 0) {
            session()->flash('error', 'Cannot delete category with posts!');
            return;
        }
        
        $category->delete();
        session()->flash('message', 'Category deleted successfully!');
        $this->resetPage();
    }

    public function duplicate($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        $newCategory = $category->replicate();
        $newCategory->title = $category->title . ' (Copy)';
        $newCategory->save();
        
        session()->flash('message', 'Category duplicated successfully!');
        $this->resetPage();
    }

    public function toggleFeatured($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $category->markAsFeatured(!$category->is_featured);
        
        session()->flash('message', 'Category updated successfully!');
        $this->resetPage();
    }

    public function togglePublished($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $category->update(['published' => !$category->published]);
        
        session()->flash('message', 'Category updated successfully!');
        $this->resetPage();
    }

    public function moveUp($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        if ($category->sort_order > 0) {
            $category->update(['sort_order' => $category->sort_order - 1]);
            session()->flash('message', 'Category moved up!');
            $this->resetPage();
        }
    }

    public function moveDown($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $category->update(['sort_order' => $category->sort_order + 1]);
        session()->flash('message', 'Category moved down!');
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->published = true;
        $this->parent_id = null;
        $this->color_code = '#6366f1';
        $this->icon = '';
        $this->sort_order = 0;
        $this->view_count = 0;
        
        // Reset settings
        $this->isFeatured = false;
        $this->showInNavigation = true;
        $this->showPostCount = true;
        $this->allowPosts = true;
        
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
        return view('livewire.admin.category-manager');
    }
}
