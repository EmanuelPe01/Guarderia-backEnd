<?php

use App\Http\Controllers\ChilController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\IngestionController;
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

    Route::post('/createNotice', [NoticeController::class, 'store']);
    Route::get('/allNotices', [NoticeController::class, 'getNotices']);
    Route::get('/getNoticeByID/{id_notice}', [NoticeController::class, 'getNoticeByID']);
    Route::put('/editNotice/{id_notice}', [NoticeController::class, 'editNotice']);
    Route::delete('/destroyNotice/{id_notice}', [NoticeController::class, 'destroyNotice']);

    Route::post('/createIngestion', [IngestionController::class, 'store']);
    Route::get('/getIngestasByGroup/{type}/{date}', [IngestionController::class, 'getIngestasByGroup']);
    Route::get('/getIngestasByChild', [IngestionController::class, 'getIngestasByChild']);
    Route::put('/editIngesta/{id_ingesta}', [IngestionController::class, 'editIngesta']);

    Route::get('/getChidrenByGroup', [GroupController::class, 'getChildByGroup']);
});

Route::post('/createGroup', [GroupController::class, 'store']);
Route::get('/allGroups', [GroupController::class, 'getAllGroups']);

Route::post('/createChild', [ChilController::class, 'store']);

Route::post('/createFood', [FoodController::class, 'store']);