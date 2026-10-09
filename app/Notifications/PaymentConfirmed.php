<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification
{
    public function __construct(private Subscription $subscription)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment received — welcome to BigRig')
            ->greeting('Welcome aboard, ' . $notifiable->firstName . '!')
            ->line('We have received your payment of ₦' . number_format($this->subscription->total_amount, 2) . '.')
            ->line('Reference: ' . $this->subscription->payment_reference)
            ->line('Course(s): ' . $this->subscription->objectives->pluck('objective')->join(', ', ' and '))
            ->line('Your instructor and training schedule are now available in your student portal.')
            ->action('Open my student portal', route('dashboard'));
    }
}
