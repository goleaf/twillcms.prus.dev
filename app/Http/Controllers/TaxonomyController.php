<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxonomyCreateRequest;
use App\Http\Requests\TaxonomyUpdateRequest;
use App\Repositories\TaxonomyRepository;
use App\Repositories\ArticleRepository;
use Aliziodev\LaravelTaxonomy\Models\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TaxonomyController extends Controller
{
    protected TaxonomyRepository $taxonomyRepository;
    protected ArticleRepository $articleRepository;

    public function __construct(
        TaxonomyRepository $taxonomyRepository, 
        ArticleRepository $articleRepository
    ) {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Display a listing of taxonomies
     */
    public function index(Request $request): View
    {
        $type = $request->get('type', 'tag');
        $taxonomies = $this->taxonomyRepository->getPaginated($type, 20);
        $statistics = $this->taxonomyRepository->getStatistics();

        return view('admin.taxonomies.index', compact('taxonomies', 'statistics', 'type'));
    }

    /**
     * Display hierarchical tree view for categories
     */
    public function tree(Request $request): View
    {
        $type = $request->get('type', 'category');
        $tree = $this->taxonomyRepository->getTree($type);

        return view('admin.taxonomies.tree', compact('tree', 'type'));
    }

    /**
     * Show the form for creating a new taxonomy
     */
    public function create(Request $request): View
    {
        $type = $request->get('type', 'tag');
        $parentTaxonomies = [];

        if ($type === 'category') {
            $parentTaxonomies = $this->taxonomyRepository->getByType('category');
        }

        return view('admin.taxonomies.create', compact('type', 'parentTaxonomies'));
    }

    /**
     * Store a newly created taxonomy
     */
    public function store(TaxonomyCreateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Prepare meta data
        if (!empty($data['meta'])) {
            $data['meta'] = is_array($data['meta']) ? $data['meta'] : json_decode($data['meta'], true);
        }

        $taxonomy = $this->taxonomyRepository->create($data);

        return redirect()
            ->route('admin.taxonomies.index', ['type' => $taxonomy->type])
            ->with('success', 'Taxonomy created successfully.');
    }

    /**
     * Display the specified taxonomy
     */
    public function show(int $id): View
    {
        $taxonomy = Taxonomy::with(['models'])->findOrFail($id);
        $articles = $this->articleRepository->getByTaxonomy($taxonomy, 12);
        $relatedTaxonomies = $this->taxonomyRepository->getByType($taxonomy->type, 10);

        return view('admin.taxonomies.show', compact('taxonomy', 'articles', 'relatedTaxonomies'));
    }

    /**
     * Show the form for editing the specified taxonomy
     */
    public function edit(int $id): View
    {
        $taxonomy = Taxonomy::findOrFail($id);
        $parentTaxonomies = [];

        if ($taxonomy->type === 'category') {
            $parentTaxonomies = $this->taxonomyRepository->getByType('category')
                ->where('id', '!=', $taxonomy->id);
        }

        return view('admin.taxonomies.edit', compact('taxonomy', 'parentTaxonomies'));
    }

    /**
     * Update the specified taxonomy
     */
    public function update(TaxonomyUpdateRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        
        // Prepare meta data
        if (!empty($data['meta'])) {
            $data['meta'] = is_array($data['meta']) ? $data['meta'] : json_decode($data['meta'], true);
        }

        $updated = $this->taxonomyRepository->update($id, $data);

        if (!$updated) {
            return back()->with('error', 'Taxonomy not found.');
        }

        return redirect()
            ->route('admin.taxonomies.index', ['type' => $data['type']])
            ->with('success', 'Taxonomy updated successfully.');
    }

    /**
     * Remove the specified taxonomy
     */
    public function destroy(int $id): RedirectResponse
    {
        $taxonomy = Taxonomy::findOrFail($id);
        $type = $taxonomy->type;
        
        $deleted = $this->taxonomyRepository->delete($id);

        if (!$deleted) {
            return back()->with('error', 'Failed to delete taxonomy.');
        }

        return redirect()
            ->route('admin.taxonomies.index', ['type' => $type])
            ->with('success', 'Taxonomy deleted successfully.');
    }

    /**
     * API endpoint to get taxonomies by type
     */
    public function apiByType(Request $request, string $type): JsonResponse
    {
        $taxonomies = $this->taxonomyRepository->getByType($type);
        
        return response()->json([
            'success' => true,
            'data' => $taxonomies->map(function ($taxonomy) {
                return [
                    'id' => $taxonomy->id,
                    'name' => $taxonomy->name,
                    'slug' => $taxonomy->slug,
                    'type' => $taxonomy->type,
                    'meta' => $taxonomy->meta,
                ];
            })
        ]);
    }

    /**
     * API endpoint to search taxonomies
     */
    public function apiSearch(Request $request): JsonResponse
    {
        $term = $request->get('q', '');
        $type = $request->get('type');
        $limit = $request->get('limit', 20);

        $taxonomies = $this->taxonomyRepository->search($term, $type, $limit);

        return response()->json([
            'success' => true,
            'data' => $taxonomies->map(function ($taxonomy) {
                return [
                    'id' => $taxonomy->id,
                    'name' => $taxonomy->name,
                    'slug' => $taxonomy->slug,
                    'type' => $taxonomy->type,
                    'meta' => $taxonomy->meta,
                ];
            })
        ]);
    }

    /**
     * Get popular taxonomies for frontend display
     */
    public function popular(Request $request): JsonResponse
    {
        $type = $request->get('type', 'tag');
        $limit = $request->get('limit', 10);

        $taxonomies = $this->taxonomyRepository->getPopular($type, $limit);

        return response()->json([
            'success' => true,
            'data' => $taxonomies
        ]);
    }

    /**
     * Get featured taxonomies
     */
    public function featured(Request $request): JsonResponse
    {
        $type = $request->get('type', 'tag');
        $limit = $request->get('limit', 5);

        $taxonomies = $this->taxonomyRepository->getFeatured($type, $limit);

        return response()->json([
            'success' => true,
            'data' => $taxonomies
        ]);
    }

    /**
     * Bulk operations on taxonomies
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $action = $request->get('action');
        $ids = $request->get('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No taxonomies selected.'
            ], 400);
        }

        switch ($action) {
            case 'delete':
                foreach ($ids as $id) {
                    $this->taxonomyRepository->delete($id);
                }
                $message = 'Selected taxonomies deleted successfully.';
                break;

            case 'feature':
                foreach ($ids as $id) {
                    $taxonomy = Taxonomy::find($id);
                    if ($taxonomy) {
                        $meta = $taxonomy->meta ?? [];
                        $meta['is_featured'] = true;
                        $this->taxonomyRepository->update($id, ['meta' => $meta]);
                    }
                }
                $message = 'Selected taxonomies marked as featured.';
                break;

            case 'unfeature':
                foreach ($ids as $id) {
                    $taxonomy = Taxonomy::find($id);
                    if ($taxonomy) {
                        $meta = $taxonomy->meta ?? [];
                        $meta['is_featured'] = false;
                        $this->taxonomyRepository->update($id, ['meta' => $meta]);
                    }
                }
                $message = 'Selected taxonomies unmarked as featured.';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action.'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Rebuild nested set for hierarchical taxonomies
     */
    public function rebuildNestedSet(Request $request): JsonResponse
    {
        $type = $request->get('type', 'category');
        
        try {
            $this->taxonomyRepository->rebuildNestedSet($type);
            
            return response()->json([
                'success' => true,
                'message' => "Nested set rebuilt successfully for {$type} taxonomies."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to rebuild nested set: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get taxonomy statistics dashboard
     */
    public function dashboard(): View
    {
        $statistics = $this->taxonomyRepository->getStatistics();
        $popularTags = $this->taxonomyRepository->getPopular('tag', 10);
        $popularCategories = $this->taxonomyRepository->getPopular('category', 10);
        $categoryTree = $this->taxonomyRepository->getTree('category');

        return view('admin.taxonomies.dashboard', compact(
            'statistics', 
            'popularTags', 
            'popularCategories', 
            'categoryTree'
        ));
    }

    /**
     * Warm up taxonomy caches
     */
    public function warmCache(): JsonResponse
    {
        try {
            $this->taxonomyRepository->warmCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Taxonomy caches warmed up successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to warm up caches: ' . $e->getMessage()
            ], 500);
        }
    }
} 