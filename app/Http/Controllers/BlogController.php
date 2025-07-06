<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Serve the Vue.js SPA for all blog routes
     */
    public function index(Request $request): View
    {
        return view('spa');
    }

    /**
     * Serve the Vue.js SPA for post detail pages
     */
    public function show(string $slug): View
    {
        return view('spa');
    }

    /**
     * Serve the Vue.js SPA for category pages
     */
    public function category(string $slug): View
    {
        return view('spa');
    }

    /**
     * Serve the Vue.js SPA for categories listing
     */
    public function categories(): View
    {
        return view('spa');
    }

    /**
     * Serve the Vue.js SPA for archive pages
     */
    public function archive(Request $request, int $year, ?int $month = null): View
    {
        return view('spa');
    }

    /**
     * Catch-all route for SPA
     */
    public function spa(): View
    {
        return view('spa');
    }
}
