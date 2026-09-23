<?php

namespace App\Notifications;

use App\Notifications\Channels\MailtrapChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;

class EmailOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $code) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (filled(config('services.mailtrap.api_token'))) {
            return [MailtrapChannel::class];
        }

        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Email OTP Code')
            ->greeting('Email verification')
            ->line('Use this OTP code to verify your email address:')
            ->line($this->code)
            ->line('This code expires in 10 minutes and can only be used once.');
    }

    /**
     * Get the Mailtrap Email Sending API representation of the notification.
     */
    public function toMailtrap(object $notifiable): MailtrapEmail
    {
        /** @var string $fromAddress */
        $fromAddress = config('services.mailtrap.from.address');
        /** @var string $fromName */
        $fromName = config('services.mailtrap.from.name');

        return (new MailtrapEmail)
            ->from(new Address($fromAddress, $fromName))
            ->to(new Address($notifiable->email, $notifiable->name))
            ->subject('Your Email OTP Code')
            ->category('Email Verification')
            ->text("Your email verification code is {$this->code}. This code expires in 10 minutes and can only be used once.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
