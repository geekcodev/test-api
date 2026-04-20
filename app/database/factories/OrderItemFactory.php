<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;


class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $this->faker->randomFloat(2, 5, 1000);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,
        ];
    }

    public function withOrder(int $orderId): OrderItemFactory
    {
        return $this->state(fn() => ['order_id' => $orderId]);
    }

    public function withProduct(int $productId): OrderItemFactory
    {
        return $this->state(fn() => ['product_id' => $productId]);
    }

    public function withQuantity(int $quantity): OrderItemFactory
    {
        return $this->state(fn() => ['quantity' => $quantity]);
    }

    public function withUnitPrice(float $unitPrice): OrderItemFactory
    {
        return $this->state(fn() => ['unit_price' => $unitPrice]);
    }

    public function withTotalPrice(float $totalPrice): OrderItemFactory
    {
        return $this->state(fn() => ['total_price' => $totalPrice]);
    }
}
