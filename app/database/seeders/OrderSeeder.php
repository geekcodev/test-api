<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\OrderItem;


class OrderSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->warn('Начинаю создавать заказы и позиции заказа...');
        if (Customer::count() == 0) {
            $this->call(CustomerSeeder::class);
        }
        if (Product::count() == 0) {
            $this->call(ProductSeeder::class);
        }
        $customers = Customer::all();
        $products = Product::all();

        foreach ($customers as $customer) {
            Order::factory(rand(1, 10))
                ->withCustomer($customer->id)
                ->create()
                ->each(function (Order $order) use ($products) {
                    $itemsToCreate = rand(1, 5);
                    for ($i = 0; $i < $itemsToCreate; $i++) {
                        $product = $products->random();
                        $quantity = rand(1, 3);
                        $unitPrice = (float)$product->price;
                        $totalPrice = (float)$quantity * $unitPrice;

                        OrderItem::factory()
                            ->withOrder($order->id)
                            ->withProduct($product->id)
                            ->withQuantity($quantity)
                            ->withUnitPrice($unitPrice)
                            ->withTotalPrice($totalPrice)
                            ->create();
                    }
                });
        }
        $this->command->info('✓ Заказы и позиции заказа успешно созданы');
    }
}
