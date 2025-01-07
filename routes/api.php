<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController, FormController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["prefix"=>"v1"], function()
{
    Route::group(["prefix"=>"auth"], function()
    {
        Route::post("login", [AuthController::class, "login"]);
        Route::middleware("auth:sanctum")->post("logout", [AuthController::class, "logout"]);
    });

    Route::middleware("auth:sanctum")->resource("forms", FormController::class);
});

