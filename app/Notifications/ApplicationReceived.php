<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReceived extends Notification
{
    public function __construct(private ?Subscription $subscription)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('We have received your application')
            ->greeting('Hello ' . $notifiable->firstName . ',')
            ->line('Thank you for applying to ' . config('app.name') . '. Your application has been received.');

        if ($this->subscription) {
            $courses = $this->subscription->objectives->pluck('objective')->join(', ', ' and ');
            $mail->line("Course(s): {$courses}")
                ->line('Amount due: ₦' . number_format($this->subscription->total_amount, 2) . ' (including VAT)');
        }

        return $mail
            ->line('Your place is confirmed and an instructor is assigned as soon as your payment is received.')
            ->action('View my application', route('application'))
            ->line('If you have any questions, simply reply to this email or contact the school.');
    }
}
