<?php

namespace App\Http\Controllers;

use App\Commands\Sale\CreateSaleCommand;
use App\Http\Requests\CreateSaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function create(CreateSaleRequest $request){
        try{
            DB::beginTransaction();
            $requestValidated = $request->validated();

            $response = CreateSaleCommand::execute($requestValidated['products']);

            if(!$response){
                DB::rollBack();
                return response()->error("Unexpected error while creating sale", 422);
            }

            DB::commit();

            return response()->success();
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->error();
        }
    }
}
