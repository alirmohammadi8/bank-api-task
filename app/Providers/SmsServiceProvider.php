<?php

namespace App\Providers;

use App\Services\SmsService\Providers\KavenegarSmsProvider;
use App\Services\SmsService\SmsServiceInterface;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(SmsServiceInterface::class, function ($app) {
            return new KavenegarSmsProvider();
            // return new TwilioSmsProvider();
            // return new GhasedakSmsProvider();
        });
    }

    public function boot(): void
    {
    }
}
