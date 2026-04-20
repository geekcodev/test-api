<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;


#[MapName(SnakeCaseMapper::class)]
class OrderItemData extends Data
{
    public function __construct(
        #[IntegerType]
        public int         $id,
        #[IntegerType, Exists('orders', 'id')]
        public int         $orderId,
        #[IntegerType, Exists('products', 'id')]
        public int         $productId,
        #[IntegerType]
        public int         $quantity,
        #[Numeric]
        public float       $unitPrice,
        #[Numeric]
        public float       $totalPrice,
        public ProductData $product,
    )
    {
    }
}
