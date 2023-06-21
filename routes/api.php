<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;


Route::post('/createRole', [RoleController::class, 'store']);

Route::post('/createUser', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login'])->name('login');


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/check-status',[UserController::class,'checkStatus']);
    Route::post('/logout', [UserController::class, 'logout']);
});

