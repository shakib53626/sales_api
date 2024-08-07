<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\MarksController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware("auth:sanctum")->group(function() {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::controller(UserController::class)->group(function(){
        Route::get('users', 'index');
        Route::post('users', 'store');
        Route::get('users/permission', 'userPermission');
        Route::get('users/{id}', 'show');
        Route::put('users/{id}', 'update');
        Route::delete('users/{id}', 'destroy');
    });

    Route::controller(RoleController::class)->group(function(){
        Route::get('roles',  'index');
        Route::post('roles',  'store');
        Route::get('roles/{id}', 'show');
        Route::put('roles/{id}', 'update');
    });

    Route::controller(SubjectController::class)->group(function(){
        Route::get('subjects', 'index');
        Route::post('subjects', 'store');
        Route::get('subjects/{id}', 'show');
        Route::put('subjects/{id}', 'update');
        Route::delete('subjects/{id}', 'destroy');
    });

    Route::controller(MarksController::class)->group(function(){
        Route::get('marks', 'index');
        Route::post('marks', 'store');
        Route::get('marks/{id}', 'show');
        Route::put('marks/{id}', 'update');
        Route::delete('marks/{id}', 'destroy');
    });

    Route::controller(OrderController::class)->group(function(){
        Route::get('orders', 'index');
        Route::post('orders', 'store');
        Route::get('orders/{id}', 'show');
        Route::put('orders/{id}', 'update');
        Route::delete('orders/{id}', 'destroy');
    });

    // Order route
});
