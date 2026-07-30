<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accountSender=Account::inRandomOrder(null)->first();
        $accountReceiver=Account::inRandomOrder(null)->first();

        while($accountSender->id == $accountReceiver->id){
            $accountSender=Account::inRandomOrder(null)->first();
        }


        return [
            'name'=>fake()->realText(20),
            'amount'=>fake()->numberBetween(1,100000),
            'date_time'=>fake()->dateTimeBetween('-1 year','now'),
            'sender_account_id'=>$accountSender->id,
            'receiver_account_id'=>$accountReceiver->id,
            'category_id'=>Category::inRandomOrder(null)->first()->id
        ];
    }
}
