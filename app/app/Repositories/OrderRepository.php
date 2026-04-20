<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Data\CreateOrderData;
use App\Data\CreateOrderItemData;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Throwable;
use Illuminate\Validation\ValidationException;

class OrderRepository extends BaseRepository
{
    protected array $defaultWith = ['customer', 'orderItems.product'];

    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Create an order with items, handling stock validation and transaction.
     *
     * @param CreateOrderData $data
     * @return Order
     * @throws Exception|Throwable
     */
    public function createOrder(CreateOrderData $data): Order
    {
        return DB::transaction(function () use ($data) {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($data->items as $index => $item) { // Added $index for validation error messaging
                /** @var CreateOrderItemData $item */
                $productId = $item->productId;
                $quantity = $item->quantity;

                /** @var Product $product */
                $product = Product::query()->lockForUpdate()->find($productId);

                if (!$product) {
                    throw ValidationException::withMessages([
                        "items.{$index}.product_id" => ["Product with ID {$productId} not found."],
                    ]);
                }

                if ($product->stock_quantity < $quantity) {
                    throw ValidationException::withMessages([
                        "items.{$index}.quantity" => ["Insufficient stock for product ID {$productId}. Available: {$product->stock_quantity}, Requested: {$quantity}."],
                    ]);
                }

                $product->stock_quantity -= $quantity;
                $product->save();

                $unitPrice = $product->price;
                $itemTotalPrice = $unitPrice * $quantity;
                $totalAmount += $itemTotalPrice;

                $orderItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotalPrice,
                ];
            }

            /** @var Order $order */
            $order = $this->create([
                'customer_id' => $data->customerId,
                'status' => OrderStatusEnum::New,
                'total_amount' => $totalAmount,
                'confirmed_at' => null,
                'shipped_at' => null,
            ]);

            $order->orderItems()->createMany($orderItemsData);

            return $order->load(['customer', 'orderItems.product']);
        });
    }

    /**
     * Get a paginated list of orders with filters.
     *
     * @param int $perPage
     * @param string|null $status
     * @param int|null $customerId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return LengthAwarePaginator
     */
    public function getOrders(
        int     $perPage = 15,
        ?string $status = null,
        ?int    $customerId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): LengthAwarePaginator
    {
        $query = $this->newQuery();

        if ($status) {
            $query->where('status', $status);
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate));
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate));
        }

        return $this->applyRelations($query)->paginate($perPage);
    }

    /**
     * Update the status of an order with transition validation.
     *
     * @param Order $order
     * @param OrderStatusEnum $newStatus
     * @return Order
     * @throws Exception
     * @throws Throwable
     */
    public function updateOrderStatus(Order $order, OrderStatusEnum $newStatus): Order
    {
        if (!$order->status->canTransitionTo($newStatus)) {
            throw new Exception("Недопустимый переход статуса из {$order->status->value} в {$newStatus->value}");
        }

        return DB::transaction(function () use ($order, $newStatus) {
            $order->status = $newStatus;

            if ($newStatus === OrderStatusEnum::Confirmed && !$order->confirmed_at) {
                $order->confirmed_at = now();
            } elseif ($newStatus === OrderStatusEnum::Shipped && !$order->shipped_at) {
                $order->shipped_at = now();
            }

            $order->save();

            return $order;
        });
    }
}
