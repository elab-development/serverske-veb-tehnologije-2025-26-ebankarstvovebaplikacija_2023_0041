<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\CurrencyService;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        if($request->user()->role==='user')
        return response()->json([
            'message' => 'OK',
            'data' => TransactionResource::collection(Transaction::with(['senderAccount.user','senderAccount.currency','receiverAccount.currency','receiverAccount.user','category'])->where(function ($query) use ($request){
                $query->whereHas('senderAccount',function ($q) use ($request){
                    $q->where('user_id','=',$request->user()->id);
                })
                ->orWhereHas('receiverAccount',function ($q) use ($request){
                    $q->where('user_id','=','$request->user()->id');
                });
            })->get())
        ],200);  
        else
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
                'sender_account_id' => 'required|exists:accounts,id',
                'receiver_account_id' => 'required|exists:accounts,id',
                'category_id' => 'required|exists:categories,id'
            ]);

        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }

        $senderAccount=Account::with('currency')->findOrFail($request->sender_account_id);
        $receiverAccount=Account::with('currency')->findOrFail($request->receiver_account_id);
        $amount=$request->amount;
        $amountToCheck=0;
        if($senderAccount->currency->id != $receiverAccount->currency->id){
            $currencyService=new CurrencyService();
            $fromCode=$senderAccount->currency->code;
            $toCode=$receiverAccount->currency->code;
            $amountToCheck=$currencyService->exchange($toCode,$fromCode,$amount);
        }else{
            $amountToCheck=$amount;
        }

        if($senderAccount->balance < $amountToCheck)
            return response()->json([
            'message' => "Insufficient funds on transaction!"
        ],422);

        $senderAccount->balance=$senderAccount->balance - $amountToCheck;
        $receiverAccount->balance=$receiverAccount->balance + $amount;

        DB::beginTransaction();

        $senderAccount->save();
        $receiverAccount->save();

        try{
            $validatedObject['date_time'] = now();
            Transaction::create($validatedObject);
        }catch(QueryException $exception){
            DB::rollBack();
            return response()->json([
                'message' => 'Internal Server Error',                
            ],500);
        }

        DB::commit();
        return response()->json([
                'message' => 'Created',                
            ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {   
        if($request->user()->role === 'user'){
            try{
                $data=new TransactionResource(Transaction::with(['senderAccount.user','senderAccount.currency','receiverAccount.currency','receiverAccount.user','category'])->where('id','=',$id,'and')->where(function ($query) use ($request){
                $query->whereHas('senderAccount',function ($q) use ($request){
                    $q->where('user_id','=',$request->user()->id);
                })->orWhereHas('receiverAccount',function ($q) use ($request){
                    $q->where('user_id','=',$request->user()->id);
                });
            })->firstOrFail()); 

            }catch(ModelNotFoundException $exception){
                return response()->json([
                'message' => 'Not Found',
                ],404);
            }
            
        }



        try{
            $transaction = Transaction::with(['senderAccount.user','senderAccount.currency','receiverAccount.currency','receiverAccount.user','category'])->findOrFail($id);
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

    public function getAccountTransactions(Request $request,string $id){
        if($request->user()->role === 'user'){
            $data=TransactionResource::collection(Transaction::with(['senderAccount.user','senderAccount.currency','receiverAccount.currency','receiverAccount.user','category'])->whereAny(['sender_account_id','receiver_account_id'],'=',$id)->where(function ($query) use ($request){
                        $query->whereHas('senderAccount',function ($q) use ($request){
                            $q->where('user_id','=',$request->user()->id);
                        })->orWhereHas('receiverAccount',function ($q) use ($request){
                            $q->where('user_id','=',$request->user()->id);
                        });
                    })->get());

                if($data->isEmpty())
                    return response()->json([
                    'message' => 'Not Found',
                    ],404);

                return response()->json([
                    'message' => 'OK',
                    'data' => $data
                ],200);
        }


        try{
            Account::findOrFail($id);
        }catch(ModelNotFoundException $exception){
            return response()->json([
            'message' => 'Not found!',
            'data' => null
            ],404);
        }

        $transactions=Transaction::with(['senderAccount.user','receiverAccount.user','senderAccount.currency','receiverAccount.currency','category'])->where('sender_account_id','=',$id,'or')->orWhere('receiver_account_id','=',$id)->get();
        return response()->json([
            'message' => 'OK',
            'data' => TransactionResource::collection($transactions)
        ],200);
    }

    public function getAccountTransactionsByCategory(Request $request,string $accountID, string $categoryID){
        if($request->user()->role==='user'){
            $data=$transactions = Transaction::with('category')->whereAny(['sender_account_id','receiver_account_id'],'=',$accountID,'or')->where('category_id', $categoryID)->where(function ($query) use ($request){
                        $query->whereHas('senderAccount',function ($q) use ($request){
                            $q->where('user_id','=',$request->user()->id);
                        })->orWhereHas('receiverAccount',function ($q) use ($request){
                            $q->where('user_id','=',$request->user()->id);
                        });
                    })->get();  
            
            if($data->isEmpty())
                return response()->json([
                'message' => 'Not Found',
                ],404);

            return response()->json([
                'message' => 'OK',
                'data' => TransactionResource::collection($data)
            ]);
        }


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

        

        $transactions = Transaction::whereAny(['sender_account_id','receiver_account_id'],'=',$accountID,'or')->where('category_id', $categoryID)->with(['category','senderAccount.user','senderAccount.currency','receiverAccount.user','receiverAccount.currency'])->get()->makeHidden('category_id');    
        return response()->json([
            'message' => 'OK',
            'data' => TransactionResource::collection($transactions)
        ],200);
    }


    public function searchTransaction(Request $request){
        $searchInput=$request->query('input');
        $transactions=[];

        if($request->user()->role === 'user')
        $transactions= Transaction::with('senderAccount.user','receiverAccount.user','senderAccount.currency','receiverAccount.currency','category')->where('name','like',"{$searchInput}%")->where(function ($query) use ($request){
            $query->whereHas('senderAccount',function ($q) use ($request){
                $q->where('user_id','=',$request->user()->id);
            })->orWhereHas('receiverAccount',function ($q) use ($request){
                $q->where('user_id',$request->user()->id);
            });
        })->get();
        else
        $transactions= Transaction::with('senderAccount.user','receiverAccount.user','senderAccount.currency','receiverAccount.currency','category')->where('name','like',"{$searchInput}%")->get();
        
        
        return response()->json([
            'message' => 'OK',
            'data' => TransactionResource::collection($transactions)
        ]);
    }
}
