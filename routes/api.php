<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\NoticeController;
use Illuminate\Support\Facades\Route;


Route::post('/createRole', [RoleController::class, 'store']);
Route::get('/allRoles', [RoleController::class, 'getAllRoles']);

Route::post('/createUser', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/allUsers', [UserController::class, 'getAllUsers']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/check-status',[UserController::class,'checkStatus']);
    Route::post('/logout', [UserController::class, 'logout']);
});

Route::post('/createGroup', [GroupController::class, 'store']);
Route::get('/allGroups', [GroupController::class, 'getAllGroups']);


Route::post('/createNotice', [NoticeController::class, 'store']);
Route::get('/allNotices', [NoticeController::class, 'getAllNotices']);

