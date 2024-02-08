<?php

namespace Database\Factories;

use App\Enums\CardStatusEnums;
use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CardFactory extends Factory
{

    protected $model = Card::class;

    /**
     * @throws \Random\RandomException
     */
    public function definition(): array
    {
        return [
            'bank_account_id' => $this->faker->randomNumber(),
            'card_number'     => $this->factoryIranianBankCardNumber(),
            'expiry_date'     => Carbon::now()->addYears(random_int(2, 5))->format('y-m'),
            'cvv2'            => $this->faker->randomNumber(4),
            'status'          => CardStatusEnums::ACTIVE->value,
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ];
    }



    public function factoryIranianBankCardNumber() {
        $bankCardBase = str_pad(mt_rand(0,999999999999999), 15, '0', STR_PAD_LEFT);
        $checkSum = $this->generateLuhnDigit($bankCardBase);
        return $bankCardBase . $checkSum;
    }

    public function generateLuhnDigit($numberBase) {
        $sum = 0;
        for ($position = 0; $position <= 14; $position++){
            $temp = $numberBase[$position];
            $temp = ($position + 1) % 2 === 0 ? $temp : $temp * 2;
            $temp = $temp > 9 ? $temp - 9 : $temp;

            $sum += $temp;
        }

        return (10 - ($sum % 10)) % 10;
    }


}
