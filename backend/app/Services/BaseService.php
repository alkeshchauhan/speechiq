<?php

namespace App\Services;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseService
{
    /**
     * The repository instance.
     */
    protected RepositoryInterface $repository;

    /**
     * BaseService constructor.
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all records.
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Find a record by ID.
     */
    public function find(int|string $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * Create a new record.
     */
    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }

    /**
     * Update a record.
     */
    public function update(int|string $id, array $attributes): bool
    {
        return $this->repository->update($id, $attributes);
    }

    /**
     * Delete a record.
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }
}
