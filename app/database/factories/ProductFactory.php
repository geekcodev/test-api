<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $categories = ['Трансмиссия', 'Выхлопная система', 'Оптика', 'Электрика', 'Интерьер'];
        return [
            'name' => $this->faker->words(rand(2, 4), true),
            'sku' => $this->faker->unique()->ean13(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 200),
            'category' => $this->faker->randomElement($categories),
        ];
    }
}
