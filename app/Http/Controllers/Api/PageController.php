<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Get all published pages
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $pages = Page::published()
                ->orderBy('position', 'asc')
                ->orderBy('title', 'asc')
                ->get()
                ->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'excerpt' => $page->excerpt,
                        'url' => $page->url,
                        'published_at' => $page->published_at?->format('Y-m-d H:i:s'),
                        'meta' => [
                            'title' => $page->meta_title ?: $page->title,
                            'description' => $page->meta_description ?: $page->excerpt,
                            'keywords' => $page->meta_keywords_string,
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $pages,
                'meta' => [
                    'total' => $pages->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load pages',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get a specific page by slug
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $page = Page::findBySlug($slug);

            if (! $page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'excerpt' => $page->excerpt,
                    'content' => $page->content,
                    'url' => $page->url,
                    'published_at' => $page->published_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $page->updated_at->format('Y-m-d H:i:s'),
                    'meta' => [
                        'title' => $page->meta_title ?: $page->title,
                        'description' => $page->meta_description ?: $page->excerpt,
                        'keywords' => $page->meta_keywords_string,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load page',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get specific static pages (Privacy, Terms, Contact)
     */
    public function getStaticPages(Request $request): JsonResponse
    {
        try {
            $staticPageSlugs = ['privacy-policy', 'terms-of-service', 'contact'];

            $pages = Page::published()
                ->whereHas('slugs', function ($query) use ($staticPageSlugs) {
                    $query->whereIn('slug', $staticPageSlugs)
                        ->where('active', true);
                })
                ->get()
                ->keyBy('slug')
                ->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'excerpt' => $page->excerpt,
                        'content' => $page->content,
                        'meta' => [
                            'title' => $page->meta_title ?: $page->title,
                            'description' => $page->meta_description ?: $page->excerpt,
                            'keywords' => $page->meta_keywords_string,
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'privacy_policy' => $pages->get('privacy-policy'),
                    'terms_of_service' => $pages->get('terms-of-service'),
                    'contact' => $pages->get('contact'),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load static pages',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
