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
        return [
            'account_number'=>fake()->unique()->numerify('###################'),
            'balance'=>fake()->randomFloat(2,0,1000000),
            'user_id'=>User::inRandomOrder(null)->first()->id,
            'currency_id'=>Currency::inRandomOrder(null)->first()->id
        ];
    }
}
