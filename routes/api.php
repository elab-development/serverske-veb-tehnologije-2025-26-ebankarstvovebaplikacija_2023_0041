<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);
Route::apiResource('categories', CategoriesController::class);
Route::apiResource('currencies', CurrencyController::class);
Route::apiResource('accounts', AccountController::class);
Route::apiResource('transactions', TransactionController::class);

Route::get('/users/{id}/accounts',[AccountController::class,'getUserAccounts']);
Route::get('/accounts/{id}/transactions',[TransactionController::class,'getAccountTransactions']);
Route::get('/accounts/{accountID}/transactions/categories/{categoryID}',[TransactionController::class,'getAccountTransactionsByCategory']);

Route::post('/currencies/convert',[CurrencyController::class,'convert']);






