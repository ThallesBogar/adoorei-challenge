<?php

namespace App\Http\Controllers;

use App\Commands\Sale\CreateSaleCommand;
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\ValidateSaleIdRequest;
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

            return response()->error();
        }
    }

    public function read(ValidateSaleIdRequest $request)
    {
        try{
            $data = GetSaleQuery::execute($request->id);

            return response()->success(description : "Sale found.", data : $data);
        }catch (\Exception | \Throwable $e){
            return response()->error();
        }
    }
}
