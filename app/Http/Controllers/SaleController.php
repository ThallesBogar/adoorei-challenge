<?php

namespace App\Http\Controllers;

use App\Commands\Sale\CreateSaleCommand;
use App\Http\Requests\CreateSaleRequest;
use App\Queries\Sale\GetSaleQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function create(CreateSaleRequest $request){
        try{
            DB::beginTransaction();
            $requestValidated = $request->validated();

            $newSaleId = CreateSaleCommand::execute($requestValidated['products']);

            if(!is_int($newSaleId)){
                DB::rollBack();
                return response()->error("Unexpected error while creating sale", 422);
            }

            DB::commit();

            $data = GetSaleQuery::execute($newSaleId);

            return response()->success(data: $data);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->error();
        }
    }
}
