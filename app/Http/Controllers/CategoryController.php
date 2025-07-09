<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryRepository $categoryRepository;
    protected ArticleRepository $articleRepository;

    public function __construct(CategoryRepository $categoryRepository, ArticleRepository $articleRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Display a listing of all categories
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        if ($search) {
            $categories = $this->categoryRepository->search($search, 20);
        } else {
            $categories = $this->categoryRepository->getAllPaginated(20);
        }

        $popularCategories = $this->categoryRepository->getPopular(10);
        $featuredCategories = $this->categoryRepository->getFeatured(5);

        return view('news.categories.index', compact('categories', 'popularCategories', 'featuredCategories', 'search'));
    }

    /**
     * Display articles filtered by category
     */
    public function show(Category $category)
    {
        // Get articles in this category
        $posts = $this->articleRepository->getByCategory($category, 12);
        
        // Get related categories
        $relatedCategories = $this->categoryRepository->getPopular(5);

        return view('news.categories.show', compact('category', 'posts', 'relatedCategories'));
    }
}
