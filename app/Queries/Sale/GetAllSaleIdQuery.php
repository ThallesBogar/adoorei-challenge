<?php

namespace App\Queries\Sale;

use App\Models\Sale;

class GetAllSaleIdQuery
{
    public static function execute() : array
    {
        return Sale::pluck('id')->toArray();
    }
}
