<?php

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a customer can create an order and product stock is reduced', function () {
    // Arrange
    $customer = Customer::factory()->create();
    $product1 = Product::factory()->create(['price' => 10.00, 'stock_quantity' => 10]);
    $product2 = Product::factory()->create(['price' => 20.00, 'stock_quantity' => 5]);

    $orderData = [
        'customer_id' => $customer->id,
        'items' => [
            ['product_id' => $product1->id, 'quantity' => 2], // 2 * 10 = 20
            ['product_id' => $product2->id, 'quantity' => 1], // 1 * 20 = 20
        ],
    ];

    // Act
    $response = $this->postJson('/api/v1/orders', $orderData);

    // Assert HTTP Response
    $response->assertStatus(201)
             ->assertJsonStructure([
                 'id',
                 'customer_id',
                 'status',
                 'total_amount',
                 'confirmed_at',
                 'shipped_at',
                 'customer' => [
                     'id',
                     'name',
                     'email',
                 ],
                 'order_items' => [
                     '*' => [
                         'id',
                         'order_id',
                         'product_id',
                         'quantity',
                         'unit_price',
                         'total_price',
                     ]
                 ]
             ]);

    // Assert Stock Reduction
    $this->assertDatabaseHas('products', [
        'id' => $product1->id,
        'stock_quantity' => 8, // 10 - 2
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product2->id,
        'stock_quantity' => 4, // 5 - 1
    ]);

    // Assert Order Details (Total Price and Status)
    $orderId = $response->json('id'); // Get id directly from root
    $this->assertDatabaseHas('orders', [
        'id' => $orderId,
        'customer_id' => $customer->id,
        'total_amount' => 40.00, // 20 + 20
        'status' => 'new', // Assuming default status
    ]);

    // Assert Order Items Details
    $this->assertDatabaseHas('order_items', [
        'order_id' => $orderId,
        'product_id' => $product1->id,
        'quantity' => 2,
        'unit_price' => 10.00,
        'total_price' => 20.00,
    ]);
    $this->assertDatabaseHas('order_items', [
        'order_id' => $orderId,
        'product_id' => $product2->id,
        'quantity' => 1,
        'unit_price' => 20.00,
        'total_price' => 20.00,
    ]);
});

test('creating an order fails if product stock is insufficient', function () {
    // Arrange
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['price' => 10.00, 'stock_quantity' => 1]); // Only 1 in stock

    $orderData = [
        'customer_id' => $customer->id,
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2], // Requesting 2, but only 1 in stock
        ],
    ];

    // Act
    $response = $this->postJson('/api/v1/orders', $orderData);

    // Assert HTTP Response
    $response->assertStatus(422); // Unprocessable Entity
    // Assert Json structure is not needed here as it's an error response

    // Assert Stock is NOT reduced
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock_quantity' => 1, // Stock should remain unchanged
    ]);

    // Assert Order is NOT created
    $this->assertDatabaseMissing('orders', [
        'customer_id' => $customer->id,
    ]);
});

test('creating an order fails with invalid customer id', function () {
    // Arrange
    $product = Product::factory()->create(['price' => 10.00, 'stock_quantity' => 10]);

    $orderData = [
        'customer_id' => 999, // Non-existent customer ID
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
    ];

    // Act
    $response = $this->postJson('/api/v1/orders', $orderData);

    // Assert HTTP Response
    $response->assertStatus(422) // Unprocessable Entity
             ->assertJsonValidationErrors(['customer_id']);

    // Assert Order is NOT created
    $this->assertDatabaseMissing('orders', [
        'customer_id' => 999,
    ]);
});

test('creating an order fails with invalid product id', function () {
    // Arrange
    $customer = Customer::factory()->create();

    $orderData = [
        'customer_id' => $customer->id,
        'items' => [
            ['product_id' => 999, 'quantity' => 2], // Non-existent product ID
        ],
    ];

    // Act
    $response = $this->postJson('/api/v1/orders', $orderData);

    // Assert HTTP Response
    $response->assertStatus(422) // Unprocessable Entity
             ->assertJsonValidationErrors(['items.0.product_id']);

    // Assert Order is NOT created
    $this->assertDatabaseMissing('orders', [
        'customer_id' => $customer->id,
    ]);
});

test('creating an order fails with zero or negative quantity', function () {
    // Arrange
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['price' => 10.00, 'stock_quantity' => 10]);

    $orderData = [
        'customer_id' => $customer->id,
        'items' => [
            ['product_id' => $product->id, 'quantity' => 0], // Zero quantity
        ],
    ];

    // Act
    $response = $this->postJson('/api/v1/orders', $orderData);

    // Assert HTTP Response
    $response->assertStatus(422) // Unprocessable Entity
             ->assertJsonValidationErrors(['items.0.quantity']);

    // Assert Order is NOT created
    $this->assertDatabaseMissing('orders', [
        'customer_id' => $customer->id,
    ]);
});
