<?php

namespace App\Services\SmsService;

use App\Models\User;
use Illuminate\Notifications\Notification;

interface SmsServiceInterface
{
    public function send(User $notifiable, Notification $notification): void;

    public function getApiClient(): void;
}
