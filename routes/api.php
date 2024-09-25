<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductConditionController;

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

Route::resource('products', ProductController::class)->except(['show']);
Route::resource('categories', CategoryController::class);
Route::get('/products/{categoryId}', [ProductController::class, 'getProductsByCategory'])->name('products.getProductsByCategory');



Route::apiResource('product-conditions', ProductConditionController::class);
// Route::put('/api/products/{id}', [ProductController::class, 'update']);
Route::get('/products/product/{id}', [ProductController::class, 'getProductById'])->name('products.getProductById');

Route::post('export-products', [ProductController::class, 'export'])->name('products.export');;

Route::post('products/import', [ProductController::class, 'import'])->name('products.import');


Route::get('/products/category/{categoryId}', [ProductController::class, 'getProductsByCategory']);




