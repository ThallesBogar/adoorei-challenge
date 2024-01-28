<?php

namespace App\Commands\Sale;

use App\Models\ListSaleStatus;
use App\Models\Sale;
use App\Models\SaleProduct;

class CreateSaleCommand
{
    public static function execute(array $products) : bool
    {
        $newSale = Sale::create([
            'list_sale_status_id' => ListSaleStatus::PENDING,
        ]);

        foreach ($products as $product) {
            SaleProduct::create([
                'sale_id'    => $newSale->id,
                'product_id' => $product['id'],
                'amount'     => $product['amount'],
            ]);
        }

        return true;
    }
}
