<?php

namespace App\Commands\Sale;

use App\Models\ListSaleStatus;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleProduct;

class CreateSaleCommand
{
    public static function execute(array $products) : int
    {
        $newSale = Sale::create([
            'list_sale_status_id' => ListSaleStatus::PENDING,
        ]);

        $saleTotalPrice = 0;
        foreach ($products as $incomingProduct) {
            $product = Product::find($incomingProduct['id']);

            SaleProduct::create([
                'sale_id'    => $newSale->id,
                'product_id' => $incomingProduct['id'],
                'amount'     => $incomingProduct['amount'],
            ]);

            $saleTotalPrice += $product['price'] * $incomingProduct['amount'];
        }
        $newSale->total_price = $saleTotalPrice;
        $newSale->save();

        return $newSale->id;
    }
}
