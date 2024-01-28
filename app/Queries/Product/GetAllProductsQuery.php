<?php

namespace App\Queries\Product;

use Illuminate\Support\Facades\DB;

class GetAllProductsQuery
{
    public static function execute() : array
    {
        $query = "SELECT
                      s.id as store_id,
                      s.name as store_name,
                      p.id as product_id,
                      lpc.name as product_category,
                      p.name as product_name,
                      p.price as product_price,
                      p.description as product_description
                  FROM products p
                      INNER JOIN stores s ON s.id = p.store_id
                      INNER JOIN list_products_categories lpc ON lpc.id = p.list_product_category_id";

        return DB::select($query);
    }
}
