<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;


#[MapName(SnakeCaseMapper::class)]
class CreateOrderItemData extends Data
{
    public function __construct(
        #[IntegerType, Exists('products', 'id')]
        public int $productId,
        #[IntegerType, Min(1)]
        public int $quantity,
    ) {
    }
}
