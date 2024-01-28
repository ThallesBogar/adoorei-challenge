<?php

use App\Models\Product;
use Illuminate\Support\Facades\Artisan;

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

    $response->assertStatus(200);
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
