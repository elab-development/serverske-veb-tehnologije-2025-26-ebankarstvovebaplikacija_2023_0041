<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'OK',
            'data' => AccountResource::collection(Account::with(['user','currency'])->get())
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validatedObject=$request->validate([
                'account_number' => 'required|string|min:19',
                'balance' => ['required','regex:/^\d{1,15}(\.\d{1,2})?$/'],
                'user_id' => 'required|exists:users,id',
                'currency_id' => 'required|exists:currencies,id'
            ]);

        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }
       
        try{
             Account::create($validatedObject);
        }catch(QueryException $exception){
            return response()->json([
                'message' => 'Internal Server Error',                
            ],500);
        }

        return response()->json([
                'message' => 'Created',                
            ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $account = Account::with(['user','currency'])->findOrFail($id);
            return response()->json([
                'message' => 'OK',
                'data' => new AccountResource($account)
            ],200);
        }catch(ModelNotFoundException $exception){
            return response()->json([
                'message' => 'Not Found',
            ],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $account=Account::findOrFail($id);
        }catch(ModelNotFoundException $exception){
             return response()->json([
                'message' => 'Not Found',                
            ],404);
        }
        
         try{
            $validatedObject=$request->validate([
                'account_number' => 'sometimes|string|min:19',
                'balance' => ['sometimes','regex:/^\d{1,15}(\.\d{1,2})?$/'],
                'user_id' => 'sometimes|exists:users,id',
                'currency_id' => 'sometimes|exists:currencies,id'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }
        
        

        if($request->has('account_number'))
            $account->account_number=$request->account_number;

        if($request->has('balance'))
            $account->balance=$request->balance;

        if($request->has('user_id'))
            $account->user_id=$request->user_id;

        if($request->has('currency_id'))
            $account->currency_id=$request->currency_id;

        $saved = $account->save();

        if(!$saved)
            return response()->json([
                'message' => 'Internal Server Error'
        ],500);

       
        return response()->json([
                'message' => 'OK'
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        try{
             $deletedRows=Account::destroy($id);

            if($deletedRows==0)
            return response()->json([
                'message' => 'Not Found'
            ],404); 

            return response()->json([
                    'message' => 'OK'
            ],200); 

        }catch(QueryException $exception){
            return response()->json([
                    'message' => 'Internal Server Error'
            ],500); 
        }

    }

    public function getUserAccounts(string $id){
        try{
            User::findOrFail($id);
        }catch(ModelNotFoundException $exception){
            return response()->json([
                'message' => 'Not Found',
                'data' => null
            ],404);
        }


        $accounts=Account::where('user_id','=',$id,null)->get();

        return response()->json([
            'message' => 'OK',
            'data' => $accounts
        ],200);
    }
}
