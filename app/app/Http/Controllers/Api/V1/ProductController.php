<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Data\ProductData;
use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepository $productRepository
    )
    {
    }

    /**
     * Display a listing of the products.
     *
     * @param Request $request
     * @return array|DataCollection|PaginatedDataCollection
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $category = $request->input('category');
        $search = $request->input('search');

        $products = $this->productRepository->getProducts((int)$perPage, $category, $search);

        return ProductData::collect($products);
    }
}
