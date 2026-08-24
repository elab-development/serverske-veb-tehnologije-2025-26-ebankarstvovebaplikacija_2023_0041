<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Currency;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Services\CurrencyService;


class ImportController extends Controller
{
   public function importUsers(Request $request){
        try{
            $validated=$request->validate([
                'file' => 'required|file|mimes:csv|max:2048'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unproccesable Entity'
            ],422);
        }
        
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(),'r');
        $header = fgetcsv($handle);

        $tempFile=tmpFile();
        $tempPath=stream_get_meta_data($tempFile)['uri'];
        fputcsv($tempFile,$header);
        $usersToSave=[];
        while(($row = fgetcsv($handle)) !== false){
            $data = array_combine($header,$row);


            $validated =Validator::make($data,[
                    'first_name' => 'required|string|max:30',
                    'last_name' => 'required|string|max:30',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:7'
                ]);

            if($validated->fails()){
                fclose($handle);
                fclose($tempFile);

                return response()->json([
                'message' => 'Unproccesable Entity'
                ],422);
            }

            $validatedData=$validated->validated();
            $validatedData['password']=Hash::make($validatedData['password']);
            $usersToSave[]=$validatedData;

            fputcsv(
                $tempFile,
                array_map( fn ($column) => $validatedData[$column],$header)
            );
        }

        fclose($handle);

        User::insert($usersToSave);

        rewind($tempFile);

        $fileName=now()->format('Y_m_d_H_i_s').'.csv';
        Storage::put('usersCSV/'.$fileName,stream_get_contents($tempFile));

        return response()->json([
            'message' => 'Users imported succesfully',
        ],201);
   }


   public function importCategories(Request $request){
        try{
            $validated=$request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unproccesable Entity'
            ],422);
        }
        
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(),'r');
        $header = fgetcsv($handle);

        $categoriesToSave=[];
        while(($row = fgetcsv($handle)) !== false){
            $data = array_combine($header,$row);


            $validated =Validator::make($data,[
                    'name' => 'required|string|max:30'
                ]);

            if($validated->fails()){
                fclose($handle);

                return response()->json([
                'message' => 'Unproccesable Entity'
                ],422);
            }

            $validatedData=$validated->validated();
            $categoriesToSave[]=$validatedData;
        }

        fclose($handle);

        Category::insert($categoriesToSave);

        $fileName = now()->format('Y_m_d_H_i_s') . '.csv';
        Storage::putFileAs(
            'categoriesCSV',
            $file,
            $fileName
        );

        return response()->json([
            'message' => 'Categories imported succesfully',
        ],201);
   }


   public function importCurrencies(Request $request){
        try{
            $validated=$request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unproccesable Entity'
            ],422);
        }
        
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(),'r');
        $header = fgetcsv($handle);

        $currenciesToSave=[];
        while(($row = fgetcsv($handle)) !== false){
            $data = array_combine($header,$row);


            $validated =Validator::make($data,[
                    'name' => 'required|string|max:30',
                    'code' => 'required|uppercase|size:3|unique:currencies,code'
                ]);

            if($validated->fails()){
                fclose($handle);

                return response()->json([
                'message' => 'Unproccesable Entity'
                ],422);
            }

            if(!CurrencyService::codeExists($data['code'])){
                fclose($handle);
                return response()->json([
                    'message' => 'Invalid currency code'
                ],422);
            }

            $validatedData=$validated->validated();
            $currenciesToSave[]=$validatedData;
        }

        fclose($handle);

        Currency::insert($currenciesToSave);

        $fileName = now()->format('Y_m_d_H_i_s') . '.csv';
        Storage::putFileAs(
            'currenciesCSV',
            $file,
            $fileName
        );

        return response()->json([
            'message' => 'Currencies imported succesfully',
        ],201);
   }


   public function importAccounts(Request $request){
        try{
            $validated=$request->validate([
                'file' => 'required|file|mimes:csv|max:2048'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unproccesable Entity'
            ],422);
        }
        
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(),'r');
        $header = fgetcsv($handle);

        $accountsToSave=[];
        while(($row = fgetcsv($handle)) !== false){
            $data = array_combine($header,$row);
            $validated =Validator::make($data,[
                    'account_number' => [
                    'required',
                    'string',
                    'regex:/^\d{19}$/',
                    'unique:accounts,account_number',
                    ],
                    'balance' => ['required','regex:/^\d{1,15}(\.\d{1,2})?$/'],
                    'user_id' => 'required|exists:users,id',
                    'currency_id' => 'required|exists:currencies,id'
                ]);

            if($validated->fails()){
                fclose($handle);

                return response()->json([
                'message' => 'Unproccesable Entity'
                ],422);
            }

            $validatedData=$validated->validated();
            $accountsToSave[]=$validatedData;
        }

        fclose($handle);

        Account::insert($accountsToSave);

        $fileName = now()->format('Y_m_d_H_i_s') . '.csv';
        Storage::putFileAs(
            'accountsCSV',
            $file,
            $fileName
        );

        return response()->json([
            'message' => 'Accounts imported succesfully',
        ],201);
   }



   public function importTransactions(Request $request){
        try{
            $validated=$request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unproccesable Entity'
            ],422);
        }
        
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(),'r');
        $header = fgetcsv($handle);

        $transactionsToSave=[];
        DB::beginTransaction();
        $counter = 1;
        while(($row = fgetcsv($handle)) !== false){
            $data = array_combine($header,$row);
            $validated =Validator::make($data,[
                    'name' => 'required|string|max:50',
                    'amount' => ['required', 'regex:/^\d{1,48}(\.\d{1,2})?$/'],
                    'sender_account_id' => 'required|exists:accounts,id',
                    'receiver_account_id' => 'required|exists:accounts,id',
                    'category_id' => 'required|exists:categories,id'
                ]);

            if($validated->fails()){
                fclose($handle);
                DB::rollback();
                return response()->json([
                'message' => 'Unproccesable Entity'
                ],422);
            }
            
            $validatedData=$validated->validated();
            $validatedData['date_time']=now();

            $senderAccount=Account::with('currency')->findOrFail($validatedData['sender_account_id']);
            $receiverAccount=Account::with('currency')->findOrFail($validatedData['receiver_account_id']);
            $amount=$validatedData['amount'];
            $amountToCheck=0;
        if($senderAccount->currency->id != $receiverAccount->currency->id){
            $fromCode=$senderAccount->currency->code;
            $toCode=$receiverAccount->currency->code;
            $amountToCheck=CurrencyService::exchange($toCode,$fromCode,$amount);
        }else{
            $amountToCheck=$amount;
        }

    
        if($senderAccount->balance < $amountToCheck)
            return response()->json([
            'message' => "Insufficient funds on transaction {$counter}!"
        ],422);

        
        $senderAccount->balance=$senderAccount->balance - $amountToCheck;
        $receiverAccount->balance=$receiverAccount->balance + $amount;
        $senderAccount->save();
        $receiverAccount->save();

        $counter=$counter+1;

            
            $transactionsToSave[]=$validatedData;
        }

        fclose($handle);

        Transaction::insert($transactionsToSave);


        DB::commit();

        $fileName = now()->format('Y_m_d_H_i_s') . '.csv';
        Storage::putFileAs(
            'transactionsCSV',
            $file,
            $fileName
        );

        return response()->json([
            'message' => 'Transactions imported succesfully',
        ],201);
   }

}
