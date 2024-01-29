<?php

use App\Models\ListSaleStatus;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleProduct;
use Illuminate\Support\Facades\Artisan;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(function (){
    config([
        'database.default'                    => env('DB_CONNECTION_TEST'),
        'database.connections.mysql.host'     => env('DB_HOST_TEST'),
        'database.connections.mysql.database' => env('DB_DATABASE_TEST'),
        'database.connections.mysql.username' => env('DB_USERNAME_TEST'),
        'database.connections.mysql.password' => env('DB_PASSWORD_TEST'),
    ]);
});

it('tests creation of valid sale', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1 = Product::first();
    $product2 = Product::skip(1)->first();
    $response = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 3,
            ],
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'description',
        'data' => [
            'sale_id',
            'status',
            'currency',
            'total_price',
            'products' => [
                '*' => [
                    'product_id',
                    'category',
                    'name',
                    'price',
                    'amount',
                ]
            ]
        ]
    ]);
});

it('tests if sale total_price is being calculated correctly when creating new sale', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1 = Product::first();
    $product2 = Product::skip(1)->first();
    $response = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 3,
            ],
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $saleId = $response->json('data.sale_id');
    $sale   = Sale::find($saleId);
    expect($sale->total_price)->toBe(3 * $product1->price + 2 * $product2->price);
});

it('tests creation of invalid sales', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $noProductsArray = postJson('/api/sales', []);
    $noProductsArray->assertStatus(422);

    $emptyProductsArray = postJson('/api/sales', ['products' => []]);
    $emptyProductsArray->assertStatus(422);

    $productWithoutId = postJson('/api/sales', [
        'products' => [
            [
                'amount' => 1,
            ]
        ]
    ]);
    $productWithoutId->assertStatus(422);

    $productWithoutAmount = postJson('/api/sales', [
        'products' => [
            [
                'id' => 1,
            ]
        ]
    ]);
    $productWithoutAmount->assertStatus(422);
});

it('retrieves a sale successfully', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product  = Product::first();
    $response = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product->id,
                'amount' => 1,
            ],
            [
                'id'     => $product->id,
                'amount' => 2,
            ],
        ],
    ]);

    $saleId   = $response->json('data.sale_id');
    $response = getJson("/api/sales/{$saleId}");
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'description',
        'data' => [
            'sale_id',
            'status',
            'currency',
            'total_price',
            'products' => [
                '*' => [
                    'product_id',
                    'category',
                    'name',
                    'price',
                    'amount',
                ]
            ]
        ]
    ]);
});

it('returns an error for retrieving a non-existent sale', function (){
    Artisan::call('migrate:fresh');
    $nonExistentId = 999999;

    $response = getJson("/api/sales/{$nonExistentId}");

    $response->assertStatus(422);
    $response->assertJson(['message' => 'error']);
});

it('validates the input for retrieving a sale with invalid id', function (){
    Artisan::call('migrate:fresh');
    $response = getJson("/api/sales/not-an-id");

    $response->assertStatus(422);
    $response->assertJson(['message' => 'error']);
});

it('lists sales successfully with correct structure and data types', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product->id,
                'amount' => 1,
            ],
            [
                'id'     => $product->id,
                'amount' => 2,
            ],
        ],
    ]);

    $getSalesResponse = getJson("/api/sales/list");
    $getSalesResponse->assertStatus(200);
    $getSalesResponse->assertJsonStructure([
        'message',
        'description',
        'data' => [
            '*' => [
                'sale_id',
                'status',
                'currency',
                'total_price',
                'products' => [
                    '*' => [
                        'product_id',
                        'category',
                        'name',
                        'price',
                        'amount',
                    ]
                ]
            ]
        ]
    ]);

    $data = $getSalesResponse->json('data');
    foreach ($data as $sale) {
        expect($sale['sale_id'])->toBeInt();
        expect($sale['status'])->toBeString();
        expect($sale['currency'])->toBeString();
        expect($sale['total_price'])->toBeNumeric();
        foreach ($sale['products'] as $product) {
            expect($product['product_id'])->toBeInt();
            expect($product['category'])->toBeString();
            expect($product['name'])->toBeString();
            expect($product['price'])->toBeNumeric();
            expect($product['amount'])->toBeInt();
        }
    }
});

it('cancel a sale successfully', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product->id,
                'amount' => 1,
            ],
            [
                'id'     => $product->id,
                'amount' => 2,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $cancelSaleResponse = postJson("/api/sales/{$saleId}/cancel", []);
    $cancelSaleResponse->assertStatus(200);
});

it('returns an error for canceling a non-existent sale', function (){
    Artisan::call('migrate:fresh');
    $nonExistentId = 999999;

    $response = postJson("/api/sales/{$nonExistentId}/cancel", []);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'error']);
});

it('validates the input for canceling a sale with invalid id', function (){
    Artisan::call('migrate:fresh');
    $response = postJson("/api/sales/not-an-id/cancel", []);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'error']);
});

it('returns an error for canceling a sale that is already canceled', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product->id,
                'amount' => 1,
            ],
            [
                'id'     => $product->id,
                'amount' => 2,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $cancelSaleResponse = postJson("/api/sales/{$saleId}/cancel", []);
    $cancelSaleResponse->assertStatus(200);

    $cancelSaleResponse = postJson("/api/sales/{$saleId}/cancel", []);
    $cancelSaleResponse->assertStatus(409);
});

it('add a product to a sale successfully', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 1,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(200);
});

it('tests if adding a product to a sale updates the total_price correctly', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 1,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $sale   = Sale::find($saleId);
    expect($sale->total_price)->toBe(1 * $product1->price + 2 * $product2->price);
});

it('tests if adding same product to a sale updates the amount correctly', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 1,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $saleProduct = SaleProduct::where('sale_id', $saleId)->where('product_id', $product1->id)->first();

    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 2,
            ],
        ],
    ]);

    $saleProductAfterAdd = SaleProduct::where('sale_id', $saleId)->where('product_id', $product1->id)->get()->toArray();

    expect(count($saleProductAfterAdd))->toBe(1)->and($saleProductAfterAdd[0]['amount'])->toBe(3);
});

it('tests if adding a product to a sale with status PROCESSING returns an error', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 1,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $sale   = Sale::find($saleId);
    $sale->list_sale_status_id = ListSaleStatus::PROCESSING;
    $sale->save();

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(409);
});

it('tests if adding a product to a sale with status PAID returns an error', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 1,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $sale   = Sale::find($saleId);
    $sale->list_sale_status_id = ListSaleStatus::PAID;
    $sale->save();

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(409);
});

it('tests if adding a product to a sale with status IN_TRANSIT_SHIPPED returns an error', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 1,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $sale   = Sale::find($saleId);
    $sale->list_sale_status_id = ListSaleStatus::IN_TRANSIT_SHIPPED;
    $sale->save();

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(409);
});

it('tests if adding a product to a sale with status DELIVERED returns an error', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 1,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $sale   = Sale::find($saleId);
    $sale->list_sale_status_id = ListSaleStatus::DELIVERED;
    $sale->save();

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(409);
});

it('tests if adding a product to a sale with status CANCELED returns an error', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 1,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $sale   = Sale::find($saleId);
    $sale->list_sale_status_id = ListSaleStatus::CANCELED;
    $sale->save();

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(409);
});

it('tests if adding a product to a sale with status RETURNED returns an error', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();
    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 1,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $sale   = Sale::find($saleId);
    $sale->list_sale_status_id = ListSaleStatus::RETURNED;
    $sale->save();

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(409);
});

it('tests if adding a product to a sale with status PARTIALLY_REFUNDED returns an error', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();

    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 3,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $sale   = Sale::find($saleId);

    $sale->list_sale_status_id = ListSaleStatus::PARTIALLY_REFUNDED;
    $sale->save();

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(409);
});

it('tests if adding a product to a sale with status PAYMENT_FAILED returns an error', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();

    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 3,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $sale   = Sale::find($saleId);

    $sale->list_sale_status_id = ListSaleStatus::PAYMENT_FAILED;
    $sale->save();

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(409);
});

it('tests if adding a product to a sale with status REFUNDED returns an error', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    $product1  = Product::first();

    $createSaleResponse = postJson('/api/sales', [
        'products' => [
            [
                'id'     => $product1->id,
                'amount' => 3,
            ],
        ],
    ]);

    $saleId = $createSaleResponse->json('data.sale_id');
    $sale   = Sale::find($saleId);

    $sale->list_sale_status_id = ListSaleStatus::REFUNDED;
    $sale->save();

    $product2  = Product::skip(1)->first();
    $addProductResponse = postJson("/api/sales/{$saleId}/products", [
        'products' => [
            [
                'id'     => $product2->id,
                'amount' => 2,
            ],
        ],
    ]);

    $addProductResponse->assertStatus(409);
});
