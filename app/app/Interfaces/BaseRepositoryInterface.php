<?php

declare(strict_types=1);

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function with(array $relations): static;
    public function withCount(array $relations): static;
    public function findById(int $id, array $with = [], array $withCount = []): ?Model;
    public function all(array $columns = ['*'], array $with = [], array $withCount = []): Collection;
    public function paginate(int $perPage = 15, array $columns = ['*'], array $with = [], array $withCount = []): LengthAwarePaginator;
    public function create(array $data): Model;
    public function update(Model $model, array $data): Model;
    public function delete(Model $model): bool;
    public function forceDelete(Model $model): bool;
    public function restore(Model $model): bool;
    public function existsById(int $id): bool;
    public function count(): int;
}
