<?php

use App\Models\ListProductCategory;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\DB;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

beforeEach(function (){
    config([
        'database.default'                    => env('DB_CONNECTION_TEST'),
        'database.connections.mysql.host'     => env('DB_HOST_TEST'),
        'database.connections.mysql.database' => env('DB_DATABASE_TEST'),
        'database.connections.mysql.username' => env('DB_USERNAME_TEST'),
        'database.connections.mysql.password' => env('DB_PASSWORD_TEST'),
    ]);
});

it('lists products successfully with correct structure and data types', function (){
    Artisan::call('migrate:fresh');

    $store = Store::create(
        [
            'name' => 'Loja de teste'
        ],
    );

    $productCategory = ListProductCategory::create([
        'name' => 'Categoria de teste'
    ]);

    // Criar produtos de exemplo no banco de dados
    Product::factory()->count(5)->create([
        'store_id'                 => $store->id,
        'list_product_category_id' => $productCategory->id
    ]);

    $response = get('/api/products/list');

    $response->assertStatus(200);

    //Verify response structure
    $response->assertJsonStructure([
        'message',
        'description',
        'data' => [
            '*' => [
                'store_id',
                'store_name',
                'product_id',
                'product_category',
                'product_name',
                'product_price',
                'product_description'
            ],
        ]
    ]);

    //Verify response data types
    $data = $response->json('data');
    foreach ($data as $product) {
        expect($product['store_id'])->toBeInt();
        expect($product['store_name'])->toBeString();
        expect($product['product_id'])->toBeInt();
        expect($product['product_category'])->toBeString();
        expect($product['product_name'])->toBeString();
        expect($product['product_price'])->toBeNumeric();
        expect($product['product_description'])->toBeString();
    }
});

it('tests generic response when database exception occurs', function () {
    // Simula uma falha no banco de dados
    DB::shouldReceive('select')->andThrow(new \Exception('Database error'));

    $response = get('/api/products/list');

    $response->assertStatus(500);
    $response->assertJson(['message' => 'error', 'description' => 'Internal Server Error']);
});
