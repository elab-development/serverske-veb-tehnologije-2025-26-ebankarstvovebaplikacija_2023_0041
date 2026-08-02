<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Nette\Schema\ValidationException;
use App\Mail\MyMail;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request){
        try{
            $validatedEmail=$request->validate([
            'email' => 'required|email|exists:users,email'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Not Found'
            ],404);
        }
        

        $token=Str::random(64);

        DB::table('password_reset_tokens')->where('email','=',$validatedEmail['email'])->delete();

        
        try{
            DB::table('password_reset_tokens')->insert([
            'email' => $validatedEmail['email'],
            'token' => Hash::make($token),
            'created_at' => now(),
            'expires_at' => now()->addMinutes(30)
            ]);
        }catch(QueryException $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
        
        Mail::to($validatedEmail['email'])->send(new MyMail($token));

        return response()->json([
            'message' => 'Reset password token has been sent to your email!'
        ],200);
    }
}
