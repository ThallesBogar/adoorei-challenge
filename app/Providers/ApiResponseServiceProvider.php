<?php

namespace App\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register() : void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $response = app(abstract : ResponseFactory::class);

        $response::macro(name : 'success', macro : function (
            string $description = "Request processed successfully",
            int $httpStatusCode= 200,
            array $data = [],
        ){
            return response()->json(['message' => 'success', 'description' => $description, 'data' => $data,],
                $httpStatusCode);
        });

        $response::macro(name : 'error', macro : function (
            string $description = "Internal Server Error",
            int $httpStatusCode = 500,
            array $data = [],
        ){
            return response()->json(['message' => 'error', 'description' => $description, 'data' => $data,],
                $httpStatusCode);
        });
    }
}
