<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExportController extends Controller
{
    public function exportUsers(){
        $users=User::all();

        $headers=[
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users.csv"'
        ];

        $callback = function () use ($users){

            $file= fopen('php://output','w');

            fputcsv($file, [
                'id',
                'first_name',
                'last_name',
                'email',
                'password',
                'role'
            ]);

            foreach($users as $user){
                fputcsv($file,[
                    $user->id,
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->password,
                    $user->role
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback,200,$headers);
    }

    
    
    
    public function exportCategories(){
        $categories = Category::all();

        $headers=[
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachemnt; filename="categories.csv"'
        ];

        $callback = function () use ($categories){
            $file = fopen('php://output','w');

            fputcsv($file,[
                'id',
                'name'
            ]);

            foreach($categories as $category){
                fputcsv($file,[
                    $category->id,
                    $category->name
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback,200,$headers);
    }

   


    public function exportCurrencies(){
        $currencies=Currency::all();

        $headers=[
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachemn; filename="currencies.csv"'
        ];

        $callback = function () use ($currencies){
            $file=fopen('php://output','w');

            fputcsv($file,[
                'id',
                'name',
                'code'
            ]);

            foreach($currencies as $currency){
                fputcsv($file,[
                    $currency->id,
                    $currency->name,
                    $currency->code
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback,200,$headers);
    }

    public function exportAccounts(){
        $accounts=Account::all();
        
        $headers=[
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="accounts.csv"'
        ];

        $callback=function () use ($accounts){
            $file = fopen('php://output','w');

            fputcsv($file,[
                'id',
                'account_number',
                'balance',
                'user_id',
                'currency_id'
            ]);

            foreach($accounts as $account){
                fputcsv($file,[
                    $account->id,
                    $account->account_number,
                    $account->balance,
                    $account->user_id,
                    $account->currency_id,
                ]);
            }

            fclose($file);
        };


        return response()->stream($callback,200,$headers);
    }





    public function exportTransactions(){
        $transactions = Transaction::all();

        $headers=[
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions.csv"'
        ];

        $callback = function () use ($transactions){
            $file=fopen('php://output','w');
            
            fputcsv($file,[
                'id',
                'name',
                'amount',
                'date_time',
                'sender_account_id',
                'receiver_account_id',
                'category_id'
            ]);

            foreach($transactions as $transaction){
                fputcsv($file,[
                    $transaction->id,
                    $transaction->name,
                    $transaction->amount,
                    $transaction->date_time,
                    $transaction->sender_account_id,
                    $transaction->receiver_account_id,
                    $transaction->category_id
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback,200,$headers);
    }


    public function exportUserTransaction(Request $request, string $id){
        $user=$request->user();

        try{
            $transaction = Transaction::where('id','=',$id,'and')->where(
            function ($query) use ($user){
                $query->whereHas('senderAccount', function ($accountQuery) use ($user){
                    $accountQuery->where('user_id',$user->id);
                })->orWhereHas('receiverAccount', function ($accountQuery) use ($user){
                    $accountQuery->where('user_id',$user->id);
                });
            })->with(['receiverAccount','senderAccount','receiverAccount.currency','category'])->firstOrFail();
        }catch(ModelNotFoundException $exception){
            return response()->json([
                'message' => 'Not found'
            ],404);
        }
        



        $pdf = Pdf::loadView('pdf.transaction',[
            'id' => $transaction->id,
            'name' => $transaction->name,
            'category' => $transaction->category->name,
            'senderAccount' => $transaction->senderAccount->account_number,
            'receiverAccount' => $transaction->receiverAccount->account_number,
            'currency' => $transaction->receiverAccount->currency->code,
            'amount' => $transaction->amount,
            'date' => $transaction->date_time->format('Y-m-d'),
            'time' => $transaction->date_time->format('H:i')   
        ]);


        return $pdf->download('transaction'.$transaction->id.'.pdf');
    }
    
}
