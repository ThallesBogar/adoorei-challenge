<?php

use App\Http\Controllers\ProductController;
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
    Route::get('/list', [ProductController::class, 'list']);
});

Route::prefix('sales')->group(function(){
    Route::post('/', 'SaleController@create');
    Route::get('/list', 'SaleController@list');
    Route::get('/{id}', 'SaleController@read');
    Route::post('/{id}/cancel', 'SaleController@cancel');
    Route::post('/{id}/products', 'SaleController@addProducts');
});
