<?php

namespace App\Http\Controllers;

use App\Queries\Product\GetAllProductsQuery;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="List all products",
     *     tags={"Products"},
     *     description="Retrieve a list of all products",
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="error"),
     *             @OA\Property(property="description", type="string", example="Internal Server Error"),
     *             @OA\Property(
     *              property="data",
     *              type="array",
     *              @OA\Items(type="string"),
     *              example={}
     *          )
     *         )
     *     )
     * )
     */
    public function list()
    {
        try{
            $products = GetAllProductsQuery::execute();

            return response()->success('Products retrieved successfully', 200, $products);
        }catch (\Exception | \Throwable $e){
            if(config('app.debug')){
                return response()->error(description : $e->getMessage(), data: $e->getTrace());
            }

            return response()->error();
        }
    }
}
