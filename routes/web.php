<?php

use App\Mail\MyMail;
use App\Models\User;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

