<?php

namespace App\Http\Requests;

use App\Rules\IranianBankCardNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class CardRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'bank_account_id' => ['required', 'integer'],
            'card_number'     => ['required', new IranianBankCardNumberRule()],
            'expiry_date'     => ['required', 'date'],
            'cvv2'            => ['required', 'integer'],
            'status'          => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
