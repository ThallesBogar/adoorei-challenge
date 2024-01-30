<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('products')->group(function(){
    Route::get('', [ProductController::class, 'list']);
});

Route::prefix('sales')->group(function(){
    Route::post('/', [SaleController::class, 'create']);
    Route::get('', [SaleController::class, 'list']);
    Route::get('/{id}', [SaleController::class, 'read']);
    Route::post('/{id}/cancel', [SaleController::class, 'cancel']);
    Route::post('/{id}/products', [SaleController::class, 'addProduct']);
});
