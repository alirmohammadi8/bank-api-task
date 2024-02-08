<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'user_id'        => ['required', 'integer'],
            'account_number' => ['required'],
            'status'         => ['required'],
            'credit'         => ['required', 'numeric'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
