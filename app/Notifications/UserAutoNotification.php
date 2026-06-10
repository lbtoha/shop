<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\NotificationTemplate;
use App\Services\Helper\ShortCodeParser;
use App\Traits\NotificationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAutoNotification extends Notification
{
    use NotificationHelper, Queueable;

    /**
     * Determine if the notification should be sent.
     */
    private array $email_template;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public NotificationType $notification_type,
        public ?array $variables = [],
    ) {
        $this->email_template = $this->getNotificationTemplate($notification_type, 'email');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        // Only add channels that have active templates with content
        if ($this->isValidEmailTemplate() && $this->isSendNotification('mail')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Determine if the notification should be sent for the given type.
     *
     * The decision is based on the system configuration.
     *
     * @param  string  $type  'mail'
     */
    public function isSendNotification($type): bool
    {
        if ($type == 'mail' && config('extra_service.system_config.email_notification.is_enabled')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if email template is valid and active
     */
    private function isValidEmailTemplate(): bool
    {
        return $this->email_template
            && $this->email_template['status'] === 'active'
            && isset($this->email_template['body']);
    }

    /**
     * Determine the notification's delivery delay.
     *
     * @return array<string, \Illuminate\Support\Carbon>
     */
    public function withDelay(object $notifiable): array
    {
        $delays = [];

        if ($this->isValidEmailTemplate()) {
            $delays['mail'] = now()->addMinutes(3);
        }

        return $delays;
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return match (true) {

            // Email
            $this->isValidEmailTemplate() && $channel === 'mail' => $this->hasValue($notifiable, 'email')
                || $this->hasAnonymousRoute($notifiable, 'mail'),

            default => false,
        };
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): ?MailMessage
    {
        return (new MailMessage)
            ->subject($this->email_template['subject'])
            ->view('emails.user-notification', [
                'notificationTemplate' => ShortCodeParser::emailBodyParse(
                    $this->email_template['body'],
                    $this->email_template['subject'],
                    $this->getModelOrRoute($notifiable, 'mail'),
                    $this->variables
                ),
            ]);
    }

    /**
     * Get notification template by type and channel
     */
    private function getNotificationTemplate(NotificationType $type, string $channel): array
    {
        $notificationTemplate = NotificationTemplate::with('bodies')->where('type', $type->value)
            ->whereRelation('bodies', 'channel', '=', $channel)
            ->first();

        if (! $notificationTemplate) {
            return [];
        }

        $body = $notificationTemplate->bodies()->active()->where('channel', $channel)->first();

        if (! $body) {
            return [];
        }

        return [
            'subject' => $body->subject,
            'body' => $body->body,
            'status' => $notificationTemplate->status,
        ];
    }
}
