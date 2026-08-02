<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request){
        try{
            $validatedObject=$request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'new_password' => 'required|min:7'
        ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',
            ],422);
        }

        $object=DB::table('password_reset_tokens')->where('email','=',$validatedObject['email'])->first();

        if($object===null)
            return response()->json([
                'message' => 'Invalid parameters'
        ],422);

        if(!Hash::check($validatedObject['token'],$object->token))
            return response()->json([
             'message' => 'Invalid parameters'   
        ]);

        $expiresAt=Carbon::parse($object->expires_at);

        if($expiresAt->isPast())
        {
          DB::table('password_reset_tokens')->where('email','=',$validatedObject['email'])->delete();
          return response()->json([
            'message' => 'Token has expired!'
          ],422);
        }

        try{
            $user = User::where('email','=',$validatedObject['email'],null)->first();
            $user->password=$validatedObject['new_password'];
            $user->save();
        }catch(QueryException $exception){
            return response()->json([
             'message' => 'Internal Server Error'   
            ],422);
        }

        DB::table('password_reset_tokens')->where('email','=',$validatedObject['email'])->delete();
        
        DB::table('personal_access_tokens')->where('tokenable_id','=',$user->id)->delete();


        return response()->json([
             'message' => 'Your password has been reseted!'   
        ],200);
    }
}
