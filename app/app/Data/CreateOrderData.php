<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;


#[MapName(SnakeCaseMapper::class)]
class CreateOrderData extends Data
{
    public function __construct(
        #[IntegerType, Exists('customers', 'id')]
        public int $customerId,
        #[DataCollectionOf(CreateOrderItemData::class), Min(1)]
        public DataCollection $items,
    ) {
    }
}
