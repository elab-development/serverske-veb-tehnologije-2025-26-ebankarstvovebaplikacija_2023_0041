<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request){
         try{
            $validatedObject=$request->validate([
                'first_name' => 'required|string|max:30',
                'last_name' => 'required|string|max:30',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:7'
            ]);

        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }

        try{
            User::create($validatedObject);
        }catch(QueryException $exception){
            return response()->json([
                'message' => 'Internal Server Error',                
            ],500);
        }

        return response()->json([
                'message' => 'Created',                
            ],201);
    }

    public function login(Request $request){
        try{
            $validatedObject=$request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }

        $user=User::where('email','=',$validatedObject['email'],null)->first();
        
        if(!$user || !Hash::check($validatedObject['password'],$user->password)){
            return response()->json([
                'message' => 'Invalid Credentials',                
            ],401);
        }
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
                'message' => 'OK',
                'token' => $token                
        ],200);

    }

    public function logout(Request $request){
         /** @var PersonalAccessToken|null $token */
        $token = $request->user()->currentAccessToken();

        $token?->delete();
        return response()->json([
            'message' => 'OK',
        ],200);
    }
}
