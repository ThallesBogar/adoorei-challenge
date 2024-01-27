<?php

use App\Models\ListProductCategory;
use App\Models\ListSaleStatus;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\Store;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

beforeEach(function (){
    config([
        'database.default'                    => env('DB_CONNECTION_TEST'),
        'database.connections.mysql.host'     => env('DB_HOST_TEST'),
        'database.connections.mysql.database' => env('DB_DATABASE_TEST'),
        'database.connections.mysql.username' => env('DB_USERNAME_TEST'),
        'database.connections.mysql.password' => env('DB_PASSWORD_TEST'),
    ]);
});

it('tests if migrations are creating all the tables and columns', function (){
    Artisan::call('migrate:fresh');

    /*
     * "table_name" => [columns]
    */
    $tables = [
        'list_sales_status'        => [
            'id',
            'name',
            'created_at',
            'updated_at'
        ],
        'list_products_categories' => [
            'id',
            'name',
            'created_at',
            'updated_at'
        ],
        'stores'                   => [
            'id',
            'name',
            'created_at',
            'updated_at'
        ],
        'products'                 => [
            'id',
            'store_id',
            'list_product_category_id',
            'name',
            'price',
            'description',
            'created_at',
            'updated_at'
        ],
        'sales'                    => [
            'id',
            'list_sale_status_id',
            'created_at',
            'updated_at'
        ],
        'sales_products'           => [
            'id',
            'sale_id',
            'product_id',
            'amount',
            'created_at',
            'updated_at',
        ],
    ];

    foreach ($tables as $table => $columns) {
        expect(Schema::hasTable($table))->toBeTrue();

        $tableSchemaColumns = Schema::getColumnListing($table);
        expect(count($tableSchemaColumns))->toBe(count($columns));

        foreach ($columns as $column) {
            expect($tableSchemaColumns)->toContain($column);
        }
    }
});

it('tests if DatabaseSeeder is beeing seeded with correct data', function (){
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');

    /*ListProductCategorySeeder Tests*/
    $productCategoryNames = [
        'Cellphones',
        'Some other category'
    ];
    foreach ($productCategoryNames as $categoryName) {
        expect(ListProductCategory::where('name', $categoryName)->exists())->toBeTrue();
    }

    /*ListSaleStatus Tests*/
    $saleStatusNames = [
        'Pending',
        'Processing',
        'Paid',
        'In Transit/Shipped',
        'Delivered',
        'Canceled',
        'Returned',
        'Partially Refunded',
        'Payment Failed',
        'Refunded'
    ];
    foreach ($saleStatusNames as $statusName) {
        expect(ListSaleStatus::where('name', $statusName)->exists())->toBeTrue();
    }
});

it('tests models relationships', function (){
    Artisan::call('migrate:fresh');

    $productCategory = ListProductCategory::create([
        'name' => 'Category 01',
    ]);

    $saleStatus = ListSaleStatus::create([
        'name' => 'Status 01',
    ]);

    $sale = Sale::create([
        'list_sale_status_id' => $saleStatus->id,
    ]);

    $store = Store::create([
        'name' => 'Store 01'
    ]);

    $product = Product::create([
        'store_id'                 => $store->id,
        'list_product_category_id' => $productCategory->id,
        'name'                     => 'Product 01',
        'description'              => 'Product 01 description',
        'price'                    => 100.00
    ]);

    $saleProduct = SaleProduct::create([
        'sale_id'    => $sale->id,
        'product_id' => $product->id,
        'amount'     => 1
    ]);

    expect($productCategory->products->count())->toBe(1);
    expect($productCategory->products->first()->list_product_category_id)->toBe($productCategory->id);

    expect($saleStatus->sales->count())->toBe(1);
    expect($saleStatus->sales->first()->list_sale_status_id)->toBe($saleStatus->id);

    expect($sale->saleProducts->count())->toBe(1);
    expect($sale->saleProducts->first()->sale_id)->toBe($sale->id);
    expect($sale->listSaleStatus->id)->toBe($saleStatus->id);

    expect($store->products->count())->toBe(1);
    expect($store->products->first()->store_id)->toBe($store->id);

    expect($product->saleProducts->count())->toBe(1);
    expect($product->saleProducts->first()->product_id)->toBe($product->id);
    expect($product->store->id)->toBe($store->id);
    expect($product->listProductCategory->id)->toBe($productCategory->id);

    expect($saleProduct->sale->id)->toBe($sale->id);
    expect($saleProduct->product->id)->toBe($product->id);
});

it('tests product table foreign keys', function (){
    Artisan::call('migrate:fresh');

    try{
        Product::create([
            'store_id'                 => 1,
            'list_product_category_id' => 1,
            'name'                     => 'Product 01',
            'description'              => 'Product 01 description',
            'price'                    => 100.00
        ]);
    }catch (Exception $e){
        expect($e->getCode())->toBe('23000');
    }
});

it('tests sales table foreign keys', function (){
    Artisan::call('migrate:fresh');

    try{
        Sale::create([
            'list_sale_status_id' => 1,
        ]);
    }catch (Exception $e){
        expect($e->getCode())->toBe('23000');
    }
});

it('tests sales_products table foreign keys', function (){
    Artisan::call('migrate:fresh');

    try{
        SaleProduct::create([
            'sale_id'    => 1,
            'product_id' => 1,
            'amount'     => 1
        ]);
    }catch (Exception $e){
        expect($e->getCode())->toBe('23000');
    }
});


