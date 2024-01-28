<?php

use App\Models\ListSaleStatus;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\get;
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

it('tests creation of valid sale', function () {
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
            'currency',
            'total_price',
            'products' => [
                '*' => [
                    'product_id',
                    'name',
                    'price',
                    'amount',
                ]
            ]
        ]
    ]);
});

it('tests if sale total_price is being calculated correctly when creating new sale', function () {
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
    $sale = Sale::find($saleId);
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

    $product = Product::first();
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

    $saleId = $response->json('data.sale_id');
    $response = getJson("/api/sales/{$saleId}");
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
        'description',
        'data' => [
            'sale_id',
            'currency',
            'total_price',
            'products' => [
                '*' => [
                    'product_id',
                    'name',
                    'price',
                    'amount',
                ]
            ]
        ]
    ]);
});

it('returns an error for retrieving a non-existent sale', function () {
    Artisan::call('migrate:fresh');
    $nonExistentId = 999999;

    $response = getJson("/api/sales/{$nonExistentId}");

    $response->assertStatus(422);
    $response->assertJson(['message' => 'error']);
});

it('validates the input for retrieving a sale with invalid id', function () {
    Artisan::call('migrate:fresh');
    $response = getJson("/api/sales/not-an-id");

    $response->assertStatus(422);
    $response->assertJson(['message' => 'error']);
});
