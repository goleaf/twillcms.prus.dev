<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    protected $moduleName = 'tags';
    protected $indexWith = ['posts'];
    protected $indexColumns = [
        'name' => [
            'title' => 'Name',
            'field' => 'name',
            'sort' => true,
        ],
        'slug' => [
            'title' => 'Slug',
            'field' => 'slug',
            'sort' => true,
        ],
        'color' => [
            'title' => 'Color',
            'field' => 'color',
            'sort' => false,
        ],
        'usage_count' => [
            'title' => 'Usage Count',
            'field' => 'usage_count',
            'sort' => true,
        ],
        'is_featured' => [
            'title' => 'Featured',
            'field' => 'is_featured',
            'sort' => true,
        ],
    ];

    protected $filters = [
        'is_featured' => [
            '1' => 'Featured',
            '0' => 'Not Featured',
        ],
    ];

    protected $defaultOrders = ['name' => 'asc'];
    protected $perPage = 20;

    public function getIndexTableData($request)
    {
        $query = Tag::withCount('posts')
            ->when($request->get('is_featured'), function ($query, $featured) {
                return $query->where('is_featured', $featured === '1');
            })
            ->when($request->get('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            });

        return $this->getIndexTableDataQuery($query);
    }

    protected function indexItemData($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'color' => "<span style='display:inline-block;width:20px;height:20px;background-color:{$item->color};border-radius:3px;'></span>",
            'usage_count' => $item->posts_count ?? $item->usage_count,
            'is_featured' => $item->is_featured ? 'Yes' : 'No',
        ];
    }

    protected function formData($request)
    {
        return [
            'colors' => [
                ['value' => '#dc2626', 'label' => 'Red'],
                ['value' => '#ea580c', 'label' => 'Orange'],
                ['value' => '#ca8a04', 'label' => 'Yellow'],
                ['value' => '#059669', 'label' => 'Green'],
                ['value' => '#0891b2', 'label' => 'Cyan'],
                ['value' => '#2563eb', 'label' => 'Blue'],
                ['value' => '#7c3aed', 'label' => 'Purple'],
                ['value' => '#ec4899', 'label' => 'Pink'],
                ['value' => '#6b7280', 'label' => 'Gray'],
            ],
        ];
    }
}
