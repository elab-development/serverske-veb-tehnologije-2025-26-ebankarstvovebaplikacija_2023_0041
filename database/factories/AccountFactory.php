<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        $users=User::all();
        $currencies=Currency::all();

        do{
            $user=$users->random();
            $currency=$currencies->random();
            $exists=Account::where('user_id','=',$user->id,'and')->where('currency_id','=',$currency->id)->exists();
        }while($exists);


        return [
            'account_number'=>fake()->unique()->numerify('###################'),
            'balance'=>fake()->randomFloat(2,0,1000000),
            'user_id'=>$user->id,
            'currency_id'=>$currency->id
        ];
    }
}
