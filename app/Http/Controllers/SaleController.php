<?php

namespace App\Http\Controllers;

use App\Commands\Sale\CancelSaleCommand;
use App\Commands\Sale\CreateSaleCommand;
use App\Commands\SaleProduct\CreateSaleProductCommand;
use App\Exceptions\SaleAlreadyCancelledException;
use App\Exceptions\SaleStatusCannotHaveNewProductsException;
use App\Http\Requests\AddProductsToSaleRequest;
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\ValidateSaleIdRequest;
use App\Queries\Sale\GetAllSaleIdQuery;
use App\Queries\Sale\GetSaleQuery;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class SaleController extends Controller
{
    /**
     * @OA\Post(
     *     path="/sales",
     *     summary="Create a new sale",
     *     tags={"Sales"},
     *     description="Create a new sale with the provided product data",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for the new sale",
     *         @OA\JsonContent(
     *             required={"products"},
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="amount", type="integer", example=2)
     *                 ),
     *                 example={{"id": 1, "amount": 2}, {"id": 2, "amount": 3}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sale created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sale created successfully."),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Sale")
     *         )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error"),
     *              @OA\Property(property="description", type="string", example="Internal Server Error"),
     *              @OA\Property(
     *               property="data",
     *               type="array",
     *               @OA\Items(type="string"),
     *               example={}
     *           )
     *          )
     *      ),
     *     @OA\Response(
     *          response=409,
     *          description="Conflict",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error"),
     *              @OA\Property(property="description", type="string", example="Sale status is beyond the point of adding new products."),
     *              @OA\Property(
     *               property="data",
     *               type="array",
     *               @OA\Items(type="string"),
     *               example={}
     *           )
     *          )
     *      )
     * )
     */
    public function create(CreateSaleRequest $request)
    {
        try{
            DB::beginTransaction();
            $requestValidated = $request->validated();

            $newSaleId = CreateSaleCommand::execute($requestValidated['products']);

            DB::commit();
            $data = GetSaleQuery::execute($newSaleId);

            return response()->success(description : "Sale created successfully.", data : $data);
        }catch (\Exception | \Throwable | SaleStatusCannotHaveNewProductsException $e){
            DB::rollBack();

            if ($e instanceof SaleStatusCannotHaveNewProductsException) {
                return response()->error(description : $e->getMessage(), httpStatusCode : $e->getCode());
            }

            if (config('app.debug')) {
                return response()->error(description : $e->getMessage(), data : $e->getTrace());
            }

            return response()->error();
        }
    }


    /**
     * @OA\Get(
     *     path="/sales/{id}",
     *     summary="Get sale details",
     *     tags={"Sales"},
     *     description="Retrieve details of a specific sale",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sale to retrieve",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sale found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sale found."),
     *             @OA\Property(property="data", ref="#/components/schemas/Sale")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Sale not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function read(ValidateSaleIdRequest $request)
    {
        try{
            $requestValidated = $request->validated();

            $data = GetSaleQuery::execute($requestValidated['id']);

            return response()->success(description : "Sale found.", data : $data);
        }catch (\Exception | \Throwable $e){
            if (config('app.debug')) {
                return response()->error(description : $e->getMessage(), data : $e->getTrace());
            }

            return response()->error();
        }
    }

    /**
     * @OA\Get(
     *     path="/sales",
     *     summary="List all sales",
     *     tags={"Sales"},
     *     description="Retrieve a list of all sales with their details",
     *     @OA\Response(
     *         response=200,
     *         description="Sales found",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Sale")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="error"),
     *             @OA\Property(property="description", type="string", example="Internal Server Error"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={}
     *             )
     *         )
     *     )
     * )
     */
    public function list()
    {
        try{
            $salesId = GetAllSaleIdQuery::execute();

            $data = [];
            foreach ($salesId as $id) {
                $data[] = GetSaleQuery::execute($id);
            }

            return response()->success(description : "Sales found.", data : $data);
        }catch (\Exception | \Throwable $e){
            if (config('app.debug')) {
                return response()->error(description : $e->getMessage(), data : $e->getTrace());
            }

            return response()->error();
        }
    }

    /**
     * @OA\Post(
     *     path="/sales/{id}/cancel",
     *     summary="Cancel a sale",
     *     tags={"Sales"},
     *     description="Cancel a specific sale by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sale to cancel",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sale canceled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sale canceled successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error while canceling sale",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error while canceling sale.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="error"),
     *             @OA\Property(property="description", type="string", example="Internal Server Error"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
     *         )
     *     ),
     *     @OA\Response(
     *          response=409,
     *          description="Conflict",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error"),
     *              @OA\Property(property="description", type="string", example="Sale already canceled."),
     *              @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
     *          )
     *      )
     * )
     */
    public function cancel(ValidateSaleIdRequest $request)
    {
        try{
            $requestValidated = $request->validated();

            $saleCanceled = CancelSaleCommand::execute($requestValidated['id']);

            if (!$saleCanceled) {
                return response()->error(description : "Error while canceling sale.", httpStatusCode : 422);
            }

            return response()->success(description : "Sale canceled successfully.");
        }catch (\Exception | \Throwable | SaleAlreadyCancelledException $e){
            if ($e instanceof SaleAlreadyCancelledException) {
                return response()->error(description : $e->getMessage(), httpStatusCode : $e->getCode());
            }

            if (config('app.debug')) {
                return response()->error(description : $e->getMessage(), data : $e->getTrace());
            }

            return response()->error();
        }
    }

    /**
     * @OA\Post(
     *     path="/sales/{id}/products",
     *     summary="Add products to a sale",
     *     tags={"Sales"},
     *     description="Add products to a specific sale by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the sale to add products to",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for adding products to the sale",
     *         @OA\JsonContent(
     *             required={"products"},
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="amount", type="integer", example=2)
     *                 ),
     *                 example={{"id": 1, "amount": 2}, {"id": 2, "amount": 3}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product added to sale successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product added to sale successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error while adding product to sale",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error while adding product to sale.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="error"),
     *             @OA\Property(property="description", type="string", example="Internal Server Error"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
     *         )
     *     ),
     *     @OA\Response(
     *          response=409,
     *          description="Sale status is beyond the point of adding new products.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error"),
     *              @OA\Property(property="description", type="string", example="Internal Server Error"),
     *              @OA\Property(property="data", type="array", @OA\Items(type="string"), example={})
     *          )
     *      )
     * )
     */
    public function addProduct(AddProductsToSaleRequest $request)
    {
        try{
            DB::beginTransaction();
            $requestValidated = $request->validated();

            $saleProductCreated = CreateSaleProductCommand::execute($requestValidated['sale_id'], $requestValidated['products']);

            if(!$saleProductCreated){
                return response()->error(description : "Error while adding product to sale.", httpStatusCode : 422);
            }

            DB::commit();

            return response()->success(description : "Product added to sale successfully.");
        }catch (\Exception | \Throwable | SaleStatusCannotHaveNewProductsException $e){
            DB::rollBack();

            if ($e instanceof SaleStatusCannotHaveNewProductsException) {
                return response()->error(description : $e->getMessage(), httpStatusCode : $e->getCode());
            }

            if (config('app.debug')) {
                return response()->error(description : $e->getMessage(), data : $e->getTrace());
            }

            return response()->error();
        }
    }
}
