<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware(['auth:sanctum','role:admin,super_admin'])->group(function (){
    Route::apiResource('users', UserController::class)->only(['store','update','destroy']);
    Route::apiResource('categories', CategoriesController::class)->only(['store','update','destroy']);
    Route::apiResource('currencies', CurrencyController::class)->only(['store','update','destroy']);
    Route::apiResource('accounts', AccountController::class)->only(['store','update','destroy']);
    Route::apiResource('transactions', TransactionController::class)->only(['store','update','destroy']);
});

Route::middleware(['auth:sanctum','role:super_admin'])->group(function (){
    Route::patch('/users/{id}/role',[UserController::class,'changeRole']);
});

Route::middleware(['auth:sanctum','role:admin,super_admin,user'])->group(function (){
    Route::apiResource('users', UserController::class)->only(['index','show']);
    Route::apiResource('categories', CategoriesController::class)->only(['index','show']);
    Route::apiResource('currencies', CurrencyController::class)->only(['index','show']);
    Route::apiResource('accounts', AccountController::class)->only(['index','show']);
    Route::apiResource('transactions', TransactionController::class)->only(['index','show']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/users/{id}/accounts',[AccountController::class,'getUserAccounts']);
    Route::get('/accounts/{id}/transactions',[TransactionController::class,'getAccountTransactions']);
    Route::get('/accounts/{accountID}/transactions/categories/{categoryID}',[TransactionController::class,'getAccountTransactionsByCategory']);
    Route::post('/currencies/convert',[CurrencyController::class,'convert']);
});

