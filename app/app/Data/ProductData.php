<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;


#[MapName(SnakeCaseMapper::class)]
class ProductData extends Data
{
    public function __construct(
        #[IntegerType]
        public int     $id,
        #[StringType]
        public string  $name,
        #[StringType]
        public string  $sku,
        #[Numeric]
        public float   $price,
        #[IntegerType]
        public int     $stockQuantity,
        #[Nullable, StringType]
        public ?string $category,
    )
    {
    }
}
