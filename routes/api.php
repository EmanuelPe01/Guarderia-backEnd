<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;


Route::post('/createRole', [RoleController::class, 'store']);

Route::post('/createUser', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/check-status',[UserController::class,'checkStatus'])->middleware('auth:sanctum');

Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [UserController::class, 'getUser'])->middleware('auth:sanctum');
