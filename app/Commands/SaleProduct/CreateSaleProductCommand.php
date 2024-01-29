<?php

namespace App\Commands\SaleProduct;

use App\Exceptions\SaleAlreadyCancelledException;
use App\Exceptions\SaleStatusCannotHaveNewProductsException;
use App\Models\ListSaleStatus;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleProduct;

class CreateSaleProductCommand
{
    public static function execute(int $saleId, array $products): bool
    {
        $sale = Sale::find($saleId);

        if ($sale->list_sale_status_id !== ListSaleStatus::PENDING) {
            throw new SaleStatusCannotHaveNewProductsException();
        }

        $priceToAdd = 0;
        foreach ($products as $product) {
            $saleProductExists = SaleProduct::where('sale_id', $saleId)->where('product_id', $product['id'])->first();

            if ($saleProductExists) {
                $saleProductExists->amount += $product['amount'];
                $saleProductExists->save();
            }else {
                SaleProduct::create([
                    'sale_id'    => $saleId,
                    'product_id' => $product['id'],
                    'amount'     => $product['amount'],
                ]);
            }

            $productModel = Product::find($product['id']);
            $priceToAdd += $product['amount'] * $productModel->price;
        }

        $sale->total_price += $priceToAdd;
        $sale->save();

        return true;
    }
}
