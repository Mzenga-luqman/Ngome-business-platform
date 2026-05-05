<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSubscriptionPaymentNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Payment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->payment->loadMissing(['user', 'subscription', 'invoice']);

        return [
            'payment_id' => $this->payment->id,
            'user_name' => $this->payment->user->name,
            'user_email' => $this->payment->user->email,
            'subscription_name' => $this->payment->subscription->name,
            'amount' => $this->payment->invoice->amount,
            'provider' => $this->payment->provider,
            'invoice_code' => $this->payment->invoice->invoice_code,
            'payment_reference' => $this->payment->payment_reference,
            'phone_number' => $this->payment->phone_number,
            'message' => $this->payment->user->name . ' submitted a ' . $this->payment->subscription->name . ' payment for approval.',
        ];
    }
}
