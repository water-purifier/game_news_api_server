<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['cors'])->group(function (){
    Route::get('/posts',[PostController::class,'index']);
    Route::get('/posts_infinity',[PostController::class,'index_limit']);
    Route::get('/posts_sitemap',[PostController::class,'index_sitemap']);
    Route::get('/posts_paths',[PostController::class,'index_paths']);
    Route::post('/posts',[PostController::class,'store']);
    Route::get('/posts/{id}',[PostController::class,'show']);
    Route::post('/posts/search',[PostController::class,'search']);
    Route::post('posts/tag',[PostController::class,'tag']);
});

