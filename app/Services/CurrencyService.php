<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    public static function exchange($fromCode,$toCode,$amount){
        $url='https://api.frankfurter.dev/v2/rate/'.$fromCode.'/'.$toCode;
        $response=Http::get($url);
        $data=$response->json();
        $rate=$data['rate'];
        $convertedAmount=round($amount * $rate,2);   
        return $convertedAmount;
    }

    public static function codeExists(string $code){
        $response = Http::get("https://api.frankfurter.dev/v2/currency/{$code}");
        return $response->successful();
    }
}
