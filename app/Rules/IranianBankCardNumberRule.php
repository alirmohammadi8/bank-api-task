<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IranianBankCardNumberRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (isset($parameters[0]) && $parameters[0] == 'seprate') {
            if (!preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $value)) {
                return false;
            }
            $value = str_replace('-', '', $value);
        }

        if (isset($parameters[0]) && $parameters[0] == 'space') {
            if (!preg_match('/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/', $value)) {
                return false;
            }
            $value = str_replace(' ', '', $value);
        }

        if (!preg_match('/^\d{16}$/', $value)) {
            return false;
        }

        $sum = 0;

        for ($position = 1; $position <= 16; $position++){
            $temp = $value[$position - 1];
            $temp = $position % 2 === 0 ? $temp : $temp * 2;
            $temp = $temp > 9 ? $temp - 9 : $temp;

            $sum += $temp;
        }

        return ($sum % 10 === 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not a valid Iranian bank card number.';
    }
}
