<?php

namespace App\Queries\Sale;

use App\Models\Sale;

class GetSaleQuery
{
    public static function execute(int $saleId) : array
    {
        $sale = Sale::with('listSaleStatus', 'saleProducts.product.listProductCategory')->find($saleId);

        if ($sale === null) {
            return [];
        }

        $saleData = [
            'sale_id'     => $sale->id,
            'status'      => $sale->listSaleStatus->name,
            'currency'    => 'USD',
            'total_price' => $sale->total_price,
            'products'    => $sale->saleProducts->map(function ($saleProduct){
                return [
                    'product_id' => $saleProduct->product_id,
                    'category'   => $saleProduct->product->listProductCategory->name,
                    'name'       => $saleProduct->product->name,
                    'price'      => $saleProduct->product->price,
                    'amount'     => $saleProduct->amount,
                ];
            }),
        ];

        return $saleData;
    }
}
