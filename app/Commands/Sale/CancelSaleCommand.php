<?php

namespace App\Commands\Sale;

use App\Exceptions\SaleAlreadyCancelledException;
use App\Models\ListSaleStatus;
use App\Models\Sale;

class CancelSaleCommand
{
    public static function execute(int $saleId) : bool
    {
        $sale = Sale::find($saleId);

        if($sale->list_sale_status_id === ListSaleStatus::CANCELED){
            throw new SaleAlreadyCancelledException();
        }

        $sale->list_sale_status_id = ListSaleStatus::CANCELED;

        return $sale->save();
    }
}
