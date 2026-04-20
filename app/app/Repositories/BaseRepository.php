<?php

declare(strict_types=1);

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\BaseRepositoryInterface;


abstract class BaseRepository implements BaseRepositoryInterface
{
    protected array $with = [];
    protected array $withCount = [];
    protected array $defaultWith = [];
    protected array $defaultWithCount = [];

    public function __construct(
        protected Model $model
    )
    {
    }

    public function with(array $relations): static
    {
        $this->with = $relations;
        return $this;
    }

    public function withCount(array $relations): static
    {
        $this->withCount = $relations;
        return $this;
    }

    protected function resetRelations(): void
    {
        $this->with = [];
        $this->withCount = [];
    }

    protected function applyRelations(Builder $query, array $with = [], array $withCount = []): Builder
    {
        $finalWith = array_unique(array_merge(
            $this->defaultWith,
            $this->with,
            $with
        ));

        $finalWithCount = array_unique(array_merge(
            $this->defaultWithCount,
            $this->withCount,
            $withCount
        ));

        if (!empty($finalWith)) {
            $query->with($finalWith);
        }

        if (!empty($finalWithCount)) {
            $query->withCount($finalWithCount);
        }

        return $query;
    }

    public function findById(int $id, array $with = [], array $withCount = []): ?Model
    {
        $query = $this->newQuery();
        $query = $this->applyRelations($query, $with, $withCount);
        $result = $query->find($id);

        $this->resetRelations();
        return $result;
    }

    public function all(array $columns = ['*'], array $with = [], array $withCount = []): Collection
    {
        $query = $this->newQuery();
        $query = $this->applyRelations($query, $with, $withCount);
        $result = $query->get($columns);

        $this->resetRelations();
        return $result;
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $with = [], array $withCount = []): LengthAwarePaginator
    {
        $query = $this->newQuery();
        $query = $this->applyRelations($query, $with, $withCount);
        $result = $query->paginate($perPage, $columns);

        $this->resetRelations();
        return $result;
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function update(Model $model, array $data): Model
    {
        return $model->update($data) ? $model : throw new Exception('Ошибка при обновлении модели');
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    public function restore(Model $model): bool
    {
        return $model->restore();
    }

    public function existsById(int $id): bool
    {
        return $this->newQuery()->where('id', $id)->exists();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    protected function newQuery(): Builder
    {
        return $this->model->newQuery();
    }
}
