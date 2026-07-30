<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {

});

Route::get('/deleteData', function () {
    Transaction::query()->delete();
    Account::query()->delete();
    Currency::query()->delete();
    Category::query()->delete();
    User::query()->delete();
    DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE transactions AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE accounts AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE currencies AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE categories AUTO_INCREMENT = 1');
    echo 'All Data from DB is deleted';
});