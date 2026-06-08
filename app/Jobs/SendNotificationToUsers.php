<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\UserManualMailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendNotificationToUsers implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $type,
        public string $user_send_type,
        public string $subject,
        public string $message_body
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = User::where('status', 'active');

        if ($this->user_send_type == 'email_verified') {
            $query->whereNotNull('email_verified_at');
        }

        if ($this->user_send_type == 'phone_verified') {
            $query->whereNotNull('phone_verified_at');
        }

        if ($this->user_send_type == 'kyc_verified') {
            $query->where('is_kyc_verified', true);
        }

        if ($this->user_send_type == 'single') {
            $query->where('id', request('user_id'));
        }

        $query->chunk(100, function ($users) {
            Notification::send($users, new UserManualMailNotification($this->subject, $this->message_body));
        });

    }
}
