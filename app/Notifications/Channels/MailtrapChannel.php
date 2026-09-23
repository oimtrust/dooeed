<?php

namespace App\Notifications\Channels;

use App\Notifications\EmailOtpNotification;
use Illuminate\Notifications\Notification;
use LogicException;
use Mailtrap\MailtrapClient;

class MailtrapChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notification instanceof EmailOtpNotification) {
            return;
        }

        /** @var string $apiToken */
        $apiToken = config('services.mailtrap.api_token');
        /** @var string $mode */
        $mode = config('services.mailtrap.mode');
        /** @var int|null $sandboxInboxId */
        $sandboxInboxId = config('services.mailtrap.sandbox_inbox_id');

        $isSandbox = $mode === 'sandbox';

        if ($isSandbox && ! $sandboxInboxId) {
            throw new LogicException('MAILTRAP_SANDBOX_INBOX_ID is required when MAILTRAP_API_MODE is sandbox.');
        }

        MailtrapClient::initSendingEmails(
            apiKey: $apiToken,
            isSandbox: $isSandbox,
            inboxId: $sandboxInboxId,
        )
            ->send($notification->toMailtrap($notifiable));
    }
}
