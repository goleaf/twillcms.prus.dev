<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $model = $this->find($id);
        return $model ? $model->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $model = $this->find($id);
        return $model ? $model->delete() : false;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }
}
