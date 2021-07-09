<?php

use App\Http\Controllers\Authentication;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Resources\UserCollection;
use App\Http\Resources\ProductCollection;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [Authentication::class, 'register']);
Route::post('/login', [Authentication::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [Authentication::class, 'logout']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/products/search/{product:sku}', [ProductController::class, 'search']);
    Route::get('/products/searchName/{name}', [ProductController::class, 'searchName']);
    
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});
Route::get('/products', [ProductController::class, 'index']);

Route::post('/products', [ProductController::class, 'store']);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::get('/users/search/{name}', [UserController::class, 'search']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::post('forgot-password', [Authentication::class, 'forgot_password']);
Route::get('reset-password', [Authentication::class, 'reset']);