<?php

namespace App\Services\SmsService\Providers;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BaseSmsProvider
{
    public $api;
    public function __construct()
    {
        $this->getApiClient();
    }

    public function getMessage($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toSms')) {
            return $notification->toSms($notifiable);
        }

        // Perhaps log an error or throw an exception in case 'toSuperSms' doesn't exist for this notification
        Log::error('Method toSms does not exist on the notification.');
        throw new RuntimeException('Method toSms does not exist on this notification.');
    }
}
