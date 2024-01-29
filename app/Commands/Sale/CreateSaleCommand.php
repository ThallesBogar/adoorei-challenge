<?php

namespace App\Commands\Sale;

use App\Commands\SaleProduct\CreateSaleProductCommand;
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

        CreateSaleProductCommand::execute($newSale->id, $products);

        return $newSale->id;
    }
}
