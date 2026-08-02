<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'OK',
            'data' => TransactionResource::collection(Transaction::with(['senderAccount.user','senderAccount.currency','receiverAccount.currency','receiverAccount.user','category'])->get())
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
                'name' => 'required|string|max:50',
                'amount' => ['required', 'regex:/^\d{1,48}(\.\d{1,2})?$/'],
                'date_time' => 'required|date_format:Y-m-d H:i:s',
                'sender_account_id' => 'required|exists:accounts,id',
                'receiver_account_id' => 'required|exists:accounts,id',
                'category_id' => 'required|exists:categories,id'
            ]);

        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }

        try{
            Transaction::create($validatedObject);
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
            $transaction = Transaction::with(['senderAccount','receiverAccount'])->findOrFail($id);
            return response()->json([
                'message' => 'OK',
                'data' => new TransactionResource($transaction)
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
            $transaction=Transaction::findOrFail($id);
        }catch(ModelNotFoundException $exception){
             return response()->json([
                'message' => 'Not Found',                
            ],404);
        }
        
         try{
            $validatedObject=$request->validate([
                'name' => 'sometimes|string|max:50',
                'amount' => ['sometimes', 'regex:/^\d{1,48}(\.\d{1,2})?$/'],
                'date_time' => 'sometimes|date_format:Y-m-d H:i:s',
                'sender_account_id' => 'sometimes|exists:accounts,id',
                'receiver_account_id' => 'sometimes|exists:accounts,id',
                'category_id' => 'sometimes|exists:categories,id'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }
        
        

        if($request->has('name'))
            $transaction->name=$request->name;

        if($request->has('amount'))
            $transaction->amount=$request->amount;

        if($request->has('date_time'))
            $transaction->date_time=$request->date_time;

        if($request->has('sender_account_id'))
            $transaction->sender_account_id=$request->sender_account_id;

        if($request->has('receiver_account_id'))
            $transaction->receiver_account_id=$request->receiver_account_id;

        if($request->has('category_id'))
            $transaction->category_id=$request->category_id;




        $saved = $transaction->save();

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
             $deletedRows=Transaction::destroy($id);

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

    public function getAccountTransactions(string $id){
        try{
            Account::findOrFail($id);
        }catch(ModelNotFoundException $exception){
            return response()->json([
            'message' => 'Not found!',
            'data' => null
            ],404);
        }

        $transactions=Transaction::where('sender_account_id','=',$id,'or')->orWhere('receiver_account_id','=',$id)->get();
        return response()->json([
            'message' => 'OK',
            'data' => $transactions
        ],200);
    }

    public function getAccountTransactionsByCategory(string $accountID, string $categoryID){
        try{
            Account::findOrFail($accountID);
        }catch(ModelNotFoundException $exception){
            return response()->json([
            'message' => 'Not found!',
            'data' => null
            ],404);
        }

         try{
            Category::findOrFail($categoryID);
        }catch(ModelNotFoundException $exception){
            return response()->json([
            'message' => 'Not found!',
            'data' => null
            ],404);
        }



        $transactions = Transaction::whereAny(['sender_account_id','receiver_account_id'],'=',$accountID,'or')->where('category_id', $categoryID)->with('category')->get()->makeHidden('category_id');    
        return response()->json([
            'message' => 'OK',
            'data' => $transactions
        ],200);
    }
}
