<?php

namespace App\Http\Requests;

use App\Rules\IranianBankCardNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->merge([
            'origin_card_number' => str_replace(['-', ' '], '', data_get($this,'origin_card_number')),
            'destination_card_number' => str_replace(['-', ' '], '', data_get($this,'destination_card_number'))
        ]);
    }

    public function rules(): array
    {
        return [
            'origin_card_number' => [
                'required', new IranianBankCardNumberRule(), Rule::exists('cards', 'card_number'),
            ],
            'destination_card_number' => [
                'required', new IranianBankCardNumberRule(), Rule::exists('cards', 'card_number'),
            ],
            'amount' => ['required', 'numeric', 'min:1000', 'max:50000000'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
