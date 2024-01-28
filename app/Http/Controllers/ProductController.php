<?php

namespace App\Http\Controllers;

use App\Queries\Product\GetAllProductsQuery;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function list()
    {
        try{
            $products = GetAllProductsQuery::execute();

            return response()->success('Products retrieved successfully', 200, $products);
        }catch (\Exception | \Throwable $e){
            return response()->error();
        }
    }
}
