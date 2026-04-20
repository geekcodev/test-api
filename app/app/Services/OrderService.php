<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Events\OrderConfirmed;

// Added this line
use App\Jobs\ExportOrderJob;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Data\CreateOrderData;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;
use Illuminate\Database\Eloquent\Model;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository
    )
    {
    }

    /**
     * Create a new order with items.
     *
     * @param CreateOrderData $data
     * @return Order
     * @throws Exception
     * @throws Throwable
     */
    public function createOrder(CreateOrderData $data): Order
    {
        return $this->orderRepository->createOrder($data);
    }

    /**
     * Update the status of an order with transition validation and dispatch job if confirmed.
     *
     * @param Order $order
     * @param OrderStatusEnum $newStatus
     * @return Order
     * @throws Exception
     * @throws Throwable
     */
    public function updateOrderStatus(Order $order, OrderStatusEnum $newStatus): Order
    {
        $updatedOrder = $this->orderRepository->updateOrderStatus($order, $newStatus);

        if ($updatedOrder->status === OrderStatusEnum::Confirmed) {
            event(new OrderConfirmed($updatedOrder->id));
        }

        return $updatedOrder;
    }

    public function getOrders(
        int     $perPage = 15,
        ?string $status = null,
        ?int    $customerId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): LengthAwarePaginator
    {
        return $this->orderRepository->getOrders($perPage, $status, $customerId, $startDate, $endDate);
    }

    public function getOrderById(int $id): Model | Order | null
    {
        return $this->orderRepository->findById($id);
    }
}
