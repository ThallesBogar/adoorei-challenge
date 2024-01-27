<?php

namespace Database\Seeders;

use App\Models\ListProductCategory;
use App\Models\Product;
use App\Models\Store;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() : void
    {
        $faker = Factory::create();

        $products = [
            ['name' => 'Celular 1', 'price' => 1800],
            ['name' => 'Celular 2', 'price' => 3200],
            ['name' => 'Celular 3', 'price' => 9800],
        ];

        foreach ($products as $product) {
            Product::create([
                'store_id' => Store::LOJA_ABC_LTDA,
                'list_product_category_id' => ListProductCategory::CELLPHONE,
                'name'        => $product['name'],
                'price'       => $product['price'],
                'description' => $faker->text(50),
            ]);
        }
    }
}
