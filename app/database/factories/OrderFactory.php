<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;


class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $status = $this->faker->boolean(40)
            ? OrderStatusEnum::New
            : $this->faker->randomElement(OrderStatusEnum::cases());
        $confirmedAt = $status !== OrderStatusEnum::New
            ? $this->faker->dateTimeBetween('-1 month')
            : null;
        $shippedAt = $status === OrderStatusEnum::Shipped
            ? $this->faker->dateTimeBetween('-1 month')
            : null;
        return [
            'customer_id' => Customer::factory(),
            'status' => $status,
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
            'confirmed_at' => $confirmedAt,
            'shipped_at' => $shippedAt,
        ];
    }

    public function withCustomer(int $customerId): OrderFactory
    {
        return $this->state(fn() => ['customer_id' => $customerId]);
    }
}
