<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Base Repository Interface
 * 
 * Defines the standard contract for all repository implementations
 * in the enterprise news system architecture.
 * 
 * @package App\Repositories\Interfaces
 */
interface RepositoryInterface
{
    /**
     * Find a model by its primary key
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model;

    /**
     * Find a model by its primary key or fail
     *
     * @param int $id
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Model;

    /**
     * Get all models
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Paginate the given query
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Create a new model
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a model
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete a model
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Add relationships to be eager loaded
     *
     * @param array $relations
     * @return static
     */
    public function with(array $relations): static;

    /**
     * Add a where clause to the query
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function where(string $column, $operator, $value = null): static;

    /**
     * Get the count of records
     *
     * @return int
     */
    public function count(): int;

    /**
     * Check if any records exist
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Cache the results for the given number of seconds
     *
     * @param int $seconds
     * @param string|null $key
     * @return static
     */
    public function cache(int $seconds, ?string $key = null): static;

    /**
     * Clear the repository cache
     *
     * @param string|null $key
     * @return bool
     */
    public function clearCache(?string $key = null): bool;
} 