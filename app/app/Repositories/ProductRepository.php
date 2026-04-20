<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Get a paginated list of products with optional category filter and name/sku search.
     *
     * @param int $perPage
     * @param string|null $category
     * @param string|null $search
     * @return LengthAwarePaginator
     */
    public function getProducts(int $perPage = 15, ?string $category = null, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->newQuery();

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        return $this->applyRelations($query)->paginate($perPage);
    }
}
