<?php

namespace Database\Factories;

use App\Enums\BankAccountStatusEnums;
use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BankAccountFactory extends Factory
{

    protected $model = BankAccount::class;

    public function definition(): array
    {
        return [
            'user_id'        => $this->faker->randomNumber(),
            'account_number' => $this->faker->unique()->numerify('##########'),
            'status'         => BankAccountStatusEnums::ACTIVE->value,
            'credit'         => $this->faker->randomFloat(2, 100, 5000),
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now(),
        ];
    }
}
