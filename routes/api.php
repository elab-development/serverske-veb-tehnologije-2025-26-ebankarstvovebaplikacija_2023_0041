<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register',[AuthController::class,'register']); 
Route::post('/login',[AuthController::class,'login']); 
Route::post('/forgot-password',[ForgotPasswordController::class,'forgotPassword']); 
Route::post('/reset-password',[ResetPasswordController::class,'resetPassword']); 

Route::middleware(['auth:sanctum','role:admin,super_admin'])->group(function (){
    Route::get('/users/export',[ExportController::class,'exportUsers']); 
    Route::get('/categories/export',[ExportController::class,'exportCategories']); 
    Route::get('/currencies/export',[ExportController::class,'exportCurrencies']); 
    Route::get('/accounts/export',[ExportController::class,'exportAccounts']); 
    Route::get('/transactions/export',[ExportController::class,'exportTransactions']); 
    Route::apiResource('users', UserController::class)->only(['store','update','destroy']); 
    Route::apiResource('categories', CategoryController::class)->only(['store','update','destroy']); 
    Route::apiResource('currencies', CurrencyController::class)->only(['store','update','destroy']); 
    Route::apiResource('accounts', AccountController::class)->only(['store','update','destroy']);#
    Route::post('/users/import',[ImportController::class,'importUsers']);
    Route::post('/categories/import',[ImportController::class,'importCategories']);
    Route::post('/currencies/import',[ImportController::class,'importCurrencies']);
    Route::post('/accounts/import',[ImportController::class,'importAccounts']);
    Route::post('/transactions/import',[ImportController::class,'importTransactions']);
});

Route::middleware(['auth:sanctum','role:super_admin'])->group(function (){
    Route::patch('/users/{id}/role',[UserController::class,'changeRole']);#
});

Route::middleware(['auth:sanctum','role:admin,super_admin,user'])->group(function (){
    Route::get('/transactions/search',[TransactionController::class,'searchTransaction']);
    Route::apiResource('transactions', TransactionController::class)->only(['index','show','store']); 
    Route::apiResource('users', UserController::class)->only(['index','show']); 
    Route::apiResource('categories', CategoryController::class)->only(['index','show']); 
    Route::apiResource('currencies', CurrencyController::class)->only(['index','show']); 
    Route::apiResource('accounts', AccountController::class)->only(['index','show']); 
    Route::post('/logout',[AuthController::class,'logout']); 
    Route::post('/currencies/convert',[CurrencyController::class,'convert']);
    Route::get('/transactions/{id}/export',[ExportController::class,'exportUserTransaction']);
    Route::get('/my-accounts',[AccountController::class,'getMyAccounts']); 
    Route::get('/accounts/{id}/transactions',[TransactionController::class,'getAccountTransactions']);
    Route::get('/accounts/{accountID}/transactions/categories/{categoryID}',[TransactionController::class,'getAccountTransactionsByCategory']);
    Route::get('/users/{id}/accounts',[AccountController::class,'getUserAccounts']); 
});
