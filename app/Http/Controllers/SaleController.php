<?php

namespace App\Http\Controllers;

use App\Commands\Sale\CancelSaleCommand;
use App\Commands\Sale\CreateSaleCommand;
use App\Exceptions\SaleAlreadyCancelledException;
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\ValidateSaleIdRequest;
use App\Queries\Sale\GetAllSaleIdQuery;
use App\Queries\Sale\GetSaleQuery;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function create(CreateSaleRequest $request)
    {
        try{
            DB::beginTransaction();
            $requestValidated = $request->validated();

            $newSaleId = CreateSaleCommand::execute($requestValidated['products']);

            DB::commit();
            $data = GetSaleQuery::execute($newSaleId);

            return response()->success(description : "Sale created successfully.", data : $data);
        }catch (\Exception | \Throwable $e){
            DB::rollBack();

            if(config('app.debug')){
                return response()->error(description : $e->getMessage(), data: $e->getTrace());
            }

            return response()->error();
        }
    }

    public function read(ValidateSaleIdRequest $request)
    {
        try{
            $data = GetSaleQuery::execute($request->id);

            return response()->success(description : "Sale found.", data : $data);
        }catch (\Exception | \Throwable $e){
            if(config('app.debug')){
                return response()->error(description : $e->getMessage(), data: $e->getTrace());
            }

            return response()->error();
        }
    }

    public function list()
    {
        try{
            $salesId = GetAllSaleIdQuery::execute();

            foreach ($salesId as $id) {
                $data[] = GetSaleQuery::execute($id);
            }

            return response()->success(description : "Sales found.", data : $data);
        }catch (\Exception | \Throwable $e){
            if(config('app.debug')){
                return response()->error(description : $e->getMessage(), data: $e->getTrace());
            }

            return response()->error();
        }
    }

    public function cancel(ValidateSaleIdRequest $request)
    {
        try{
            $saleId = $request->id;

            $saleCanceled = CancelSaleCommand::execute($saleId);

            if (!$saleCanceled) {
                return response()->error(description : "Error while canceling sale.", httpStatusCode : 422);
            }

            return response()->success(description : "Sale canceled successfully.");
        }catch (\Exception | \Throwable | SaleAlreadyCancelledException $e){
            if($e instanceof SaleAlreadyCancelledException){
                return response()->error(description: $e->getMessage(), httpStatusCode: $e->getCode());
            }

            if(config('app.debug')){
                return response()->error(description : $e->getMessage(), data: $e->getTrace());
            }

            return response()->error();
        }
    }
}
