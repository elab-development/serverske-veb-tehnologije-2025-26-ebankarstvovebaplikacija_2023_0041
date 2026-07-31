<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'OK',
            'data' => Currency::all()
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
                'name' => 'required|string|max:30',
                'code' => 'required|uppercase|size:3',
            ]);

        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }

        try{
            Currency::create($validatedObject);
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
            $currency = Currency::findOrFail($id);
            return response()->json([
                'message' => 'OK',
                'data' => $currency
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
            $currency=Currency::findOrFail($id);
        }catch(ModelNotFoundException $exception){
             return response()->json([
                'message' => 'Not Found',                
            ],404);
        }
        
         try{
            $validatedObject=$request->validate([
                'name' => 'sometimes|string|max:30',
                'code' => 'sometimes|uppercase|size:3',
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }
        
        

        if($request->has('name'))
            $currency->name=$request->name;

        if($request->has('code'))
            $currency->code=$request->code;



        $saved = $currency->save();

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
             $deletedRows=Currency::destroy($id);

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


    public function convert(Request $request){

        try{
            $validatedObject=$request->validate([
            'from_id' => 'required|exists:currencies,id',
            'to_id' => 'required|exists:currencies,id',
            'amount' => 'required|decimal:0,2'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity'
            ],422);
        }

        $currencyFrom=Currency::findOrFail($request->from_id);
        $currencyTo=Currency::findOrFail($request->to_id);

        $fromCode=$currencyFrom->code;
        $toCode=$currencyTo->code;

       

        $url='https://api.frankfurter.dev/v2/rate/'.$fromCode.'/'.$toCode;
        $response=Http::get($url);
        $data=$response->json();

        $rate=$data['rate'];
        $convertedAmount=round($request->amount * $rate,2);    

        return response()->json([
            'message' => 'OK',
            'data' => $convertedAmount
        ],200);

    }
}
