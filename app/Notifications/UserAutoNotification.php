<?php

namespace App\Notifications;

use App\Broadcasting\FirebasePushNotificationChannel;
use App\Broadcasting\SmsChannel;
use App\Enums\NotificationType;
use App\Enums\NotifyEventType;
use App\Models\NotificationTemplate;
use App\Services\Helper\ShortCodeParser;
use App\Traits\NotificationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAutoNotification extends Notification
{
    use Queueable, NotificationHelper;

    /**
     * Determine if the notification should be sent.
     */

    private array $email_template;

    private array $sms_template;

    private array $push_notification_template;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public NotificationType $notification_type,
        public ?array $variables = [],
    ) {
        $this->email_template = $this->getNotificationTemplate($notification_type, 'email');
        $this->sms_template = $this->getNotificationTemplate($notification_type, 'sms');
        $this->push_notification_template = $this->getNotificationTemplate($notification_type, 'push');
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

        if ($this->isValidSmsTemplate() && $this->isSendNotification('sms')) {
            $channels[] = SmsChannel::class;
        }

        if ($this->isValidPushNotificationTemplate() && $this->isSendNotification('push')) {
            $channels[] = FirebasePushNotificationChannel::class;
        }

        return $channels;
    }

    /**
     * Determine if the notification should be sent for the given type.
     *
     * The decision is based on the system configuration.
     *
     * @param  string  $type  'mail' or 'sms'
     */
    public function isSendNotification($type): bool
    {
        if ($type == 'mail' && config('extra_service.system_config.email_notification.is_enabled')) {
            return true;
        }

        if ($type == 'sms' && config('extra_service.system_config.sms_notification')) {
            return true;
        }

        if ($type == 'push' && config('extra_service.system_config.push_notification.is_enabled')) {
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
     * Determine if SMS template is valid and active
     */
    private function isValidSmsTemplate(): bool
    {
        return $this->sms_template
            && $this->sms_template['status'] === 'active'
            && isset($this->sms_template['body']);
    }

    /**
     * Determine if PUSH template is valid and active
     */
    private function isValidPushNotificationTemplate(): bool
    {
        return $this->push_notification_template
            && $this->push_notification_template['status'] === 'active'
            && isset($this->push_notification_template['body']);
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

        if ($this->isValidSmsTemplate()) {
            $delays[SmsChannel::class] = now()->addMinutes(3);
        }

        if ($this->isValidPushNotificationTemplate()) {
            $delays[FirebasePushNotificationChannel::class] = now()->addMinutes(3);
        }

        return $delays;
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return match (true) {

            // SMS
            $this->isValidSmsTemplate() && $channel === SmsChannel::class =>
                $this->hasValue($notifiable, 'phone')
                || $this->hasAnonymousRoute($notifiable, 'sms'),

            // Push Notification
            $this->isValidPushNotificationTemplate() && $channel === FirebasePushNotificationChannel::class =>
                $this->hasValue($notifiable, 'fcm_token')
                || $this->hasAnonymousRoute($notifiable, 'push'),

            // Email
            $this->isValidEmailTemplate() && $channel === 'mail' =>
                $this->hasValue($notifiable, 'email')
                || $this->hasAnonymousRoute($notifiable, 'mail'),

            default => false,
        };
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): ?MailMessage
    {
        return (new MailMessage())
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
     * Get the sms representation of the notification.
     */
    public function toSMS(object $notifiable): ?array
    {
        return [
            'subject' => $this->sms_template['subject'],
            'message' => ShortCodeParser::smsDefaultBodyParse(
                $this->sms_template['body'],
                $this->getModelOrRoute($notifiable, 'sms'),
                $this->variables
            ),
        ];
    }

    /**
     * Get the sms representation of the notification.
     */
    public function toPushNotification(object $notifiable): ?array
    {
        $message = ShortCodeParser::pushDefaultBodyParse(
            $this->push_notification_template['body'],
            $this->getModelOrRoute($notifiable, 'push'),
            $this->variables
        );

        $notifiable->notifications()->create([
            'type' => NotifyEventType::AUTO,
            'data' => $message,
        ]);

        return [
            'title' => $this->push_notification_template['subject'],
            'message' => $message,
            'image' => $this->variables['image'] ?? null,
            'data' => array_merge([
                'notification_type' => $this->notification_type->value,
            ], $this->variables['data'] ?? []),
        ];
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
