<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertNumbersToEnglishMiddleware
{

    public function handle(Request $request, Closure $next)
    {

        $input = $request->all();
        array_walk_recursive($input, static function (&$item) {
            if (is_string($item)) {
                $item = strtr($item, ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9', '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
