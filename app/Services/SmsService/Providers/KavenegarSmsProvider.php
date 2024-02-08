<?php

namespace App\Services\SmsService\Providers;

use App\Models\User;
use App\Services\SmsService\Providers\BaseSmsProvider;
use App\Services\SmsService\SmsServiceInterface;
use Illuminate\Notifications\Notification;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\KavenegarApi;

class KavenegarSmsProvider extends BaseSmsProvider implements SmsServiceInterface
{

    public function send(User $notifiable, Notification $notification): void
    {
        $sender = config('sms_providers.kavenegar.line_number');
        $receptor = $notifiable->phone_number;
        $message = $this->getMessage($notifiable, $notification);
        $this->api->Send($sender, $receptor, $message);
    }

    public function getApiClient(): void
    {
        $this->api = new KavenegarApi(config('sms_providers.kavenegar.api_key'));
    }
}
