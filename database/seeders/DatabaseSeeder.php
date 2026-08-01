<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email' => 'superadmin@admin.com',
        'password' => Hash::make('super123'),
        'role' => 'super_admin',
        ]);

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            CurrencySeeder::class,
            AccountSeeder::class,
            TransactionSeeder::class
        ]);
    }
}
