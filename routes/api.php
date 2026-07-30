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











