<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Data\OrderData;
use App\Data\CreateOrderData;
use Illuminate\Http\Request;
use Spatie\LaravelData\DataCollection;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Throwable;
use App\Repositories\OrderRepository;
use Illuminate\Http\JsonResponse;
use Spatie\LaravelData\PaginatedDataCollection;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService    $orderService,
        protected OrderRepository $orderRepository
    )
    {
    }

    /**
     * Display a listing of the orders.
     *
     * @param Request $request
     * @return array|DataCollection|PaginatedDataCollection
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');
        $customerId = $request->input('customer_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $orders = $this->orderRepository->getOrders(
            (int)$perPage,
            $status,
            $customerId ? (int)$customerId : null,
            $startDate,
            $endDate
        );

        return OrderData::collect($orders);
    }

    /**
     * Store a newly created order in storage.
     *
     * @param CreateOrderData $data
     * @return OrderData|JsonResponse
     * @throws Throwable
     */
    public function store(CreateOrderData $data): OrderData|JsonResponse
    {
        $order = $this->orderService->createOrder($data);
        return OrderData::fromModel($order);
    }

    /**
     * Display the specified order.
     *
     * @param int $id
     * @return OrderData
     */
    public function show(int $id): OrderData
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            abort(Response::HTTP_NOT_FOUND, 'Order not found');
        }

        return OrderData::fromModel($order);
    }

    /**
     * Update the status of the specified order.
     *
     * @param Request $request
     * @param Order $order
     * @return OrderData
     * @throws Exception
     * @throws Throwable
     */
    public function updateStatus(Request $request, Order $order): OrderData
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_map(fn($case) => $case->value, OrderStatusEnum::cases())),
        ]);

        $newStatus = OrderStatusEnum::from($request->input('status'));

        $updatedOrder = $this->orderService->updateOrderStatus($order, $newStatus);

        return OrderData::fromModel($updatedOrder);
    }
}
