<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository extends BaseRepository
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function getPublishedPosts(): Collection
    {
        return $this->model->published()->with(['user', 'categories'])->get();
    }

    public function getPublishedPostsPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->published()
            ->with(['user', 'categories'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function getPostsByCategory(int $categoryId): Collection
    {
        return $this->model->published()
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->with(['user', 'categories'])
            ->get();
    }

    public function getPostsByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->with(['user', 'categories'])
            ->get();
    }

    public function search(string $query): Collection
    {
        return $this->model->published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->with(['user', 'categories'])
            ->get();
    }
}
