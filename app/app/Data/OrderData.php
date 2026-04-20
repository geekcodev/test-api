<?php

declare(strict_types=1);

namespace App\Data;

use DateTimeImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\MapName;
use Illuminate\Support\LazyCollection;
use Spatie\LaravelData\Attributes\Validation\Exists;


#[MapName(SnakeCaseMapper::class)]
class OrderData extends Data
{
    public function __construct(
        #[IntegerType]
        public int                                                              $id,
        #[IntegerType, Exists('customers', 'id')]
        public int                                                              $customerId,
        #[Enum(OrderStatusEnum::class)]
        public OrderStatusEnum                                                  $status,
        #[Numeric]
        public float                                                            $totalAmount,
        #[Nullable, WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i:s', 'Y-m-d\TH:i:sP', 'Y-m-d\TH:i:s'], type: DateTimeImmutable::class)]
        public ?DateTimeImmutable                                               $confirmedAt,
        #[Nullable, WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i:s', 'Y-m-d\TH:i:sP', 'Y-m-d\TH:i:s'], type: DateTimeImmutable::class)]
        public ?DateTimeImmutable                                               $shippedAt,
        public CustomerData                                                     $customer,
        public Collection | LazyCollection | DataCollection | Paginator | array $orderItems,
    )
    {
    }

    public static function fromModel(Order $order): self
    {
        return new self(
            id: $order->id,
            customerId: $order->customer_id,
            status: $order->status,
            totalAmount: (float)$order->total_amount,
            confirmedAt: $order->confirmed_at,
            shippedAt: $order->shipped_at,
            customer: CustomerData::from($order->customer),
            orderItems: OrderItemData::collect($order->orderItems),
        );
    }
}
