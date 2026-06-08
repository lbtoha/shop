<?php

namespace App\Notifications;

use App\Services\Helper\ShortCodeParser;
use App\Traits\NotificationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriberNotification extends Notification
{
    use NotificationHelper, Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $subject,
        public string $message_body,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Determine the notification's delivery delay.
     *
     * @return array<string, \Illuminate\Support\Carbon>
     */
    public function withDelay(object $notifiable): array
    {
        return [
            'mail' => now()->addMinutes(3),
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.user-notification', [
                'notificationTemplate' => ShortCodeParser::emailBodyParse(
                    $this->message_body,
                    $this->subject,
                    [
                        'unsubscribe_url' => null,
                    ]
                ),
            ]);
    }
}
