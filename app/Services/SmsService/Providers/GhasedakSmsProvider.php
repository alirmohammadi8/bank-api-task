<?php

namespace App\Services\SmsService\Providers;

use App\Models\User;
use App\Services\SmsService\SmsServiceInterface;
use Ghasedak\GhasedakApi;
use Illuminate\Notifications\Notification;

class GhasedakSmsProvider extends BaseSmsProvider implements SmsServiceInterface
{

    public function send(User $notifiable, Notification $notification): void
    {
        $message = $this->getMessage($notifiable, $notification);

        $this->api->SendSimple(
            $notifiable->phone_number, // receptor
            $message, // message
            config('sms_providers.ghasedak.line_number') // choose a line number from your account
        );
    }

    public function getApiClient(): void
    {
        $this->api = new GhasedakApi( config('sms_providers.ghasedak.api_key'));
    }
}
