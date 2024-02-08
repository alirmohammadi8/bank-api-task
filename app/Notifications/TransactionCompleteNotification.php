<?php

namespace App\Notifications;

use App\Models\User;
use App\Services\SmsService\SmsServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionCompleteNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $type,
        public string $amount,
    )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [app(SmsServiceInterface::class)];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toSms(User $notifiable): string
    {
        return __("sms.transaction_complete.{$this->type}",['user' => $notifiable, 'amount' => $this->amount]);
    }
}
