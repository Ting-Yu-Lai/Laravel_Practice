<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MenuController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']);

/*  
    菜單模組 (Menu CRUD API)
*/
Route::prefix('menus')->name('menus.')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('index'); // 取得所有菜單
    Route::post('/', [MenuController::class, 'store'])->name('store'); // 創建新菜單
    Route::get('/{id}', [MenuController::class, 'show'])->name('show'); // 取得特定菜單
    Route::put('/{id}', [MenuController::class, 'update'])->name('update'); // 更新特定菜單
    Route::delete('/{id}', [MenuController::class, 'destroy'])->name('destroy'); // 刪除特定菜單
});