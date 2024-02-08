<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\BankAccount;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::all()->each(fn($user) => BankAccount::factory()->count(random_int(1,4))->create(['user_id' => $user->id]));

        BankAccount::all()->each(fn($banks) => Card::factory()->count(random_int(1,3))->create(['bank_account_id' => $banks->id]));
    }
}
