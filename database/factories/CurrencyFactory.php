<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currency = fake()->unique()->randomElement([
        ['name' => 'Serbian Dinar', 'code' => 'RSD'],
        ['name' => 'Euro', 'code' => 'EUR'],
        ['name' => 'United States Dollar', 'code' => 'USD'],
        ['name' => 'British Pound Sterling', 'code' => 'GBP'],
        ['name' => 'Swiss Franc', 'code' => 'CHF'],
        ['name' => 'Japanese Yen', 'code' => 'JPY'],
        ['name' => 'Canadian Dollar', 'code' => 'CAD'],
        ['name' => 'Australian Dollar', 'code' => 'AUD'],
        ['name' => 'Chinese Yuan Renminbi', 'code' => 'CNY'],
        ['name' => 'Russian Ruble', 'code' => 'RUB'],
        ]);
        
        return $currency;
    }
}
