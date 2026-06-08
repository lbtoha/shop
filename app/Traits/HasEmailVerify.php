<?php

namespace App\Traits;

use App\Enums\NotificationType;
use App\Exceptions\CustomWebException;
use App\Notifications\UserAutoNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

trait HasEmailVerify
{
    use EmailAndPhoneOTPVerification;

    public function sendEmailVerificationNotification()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ]
        );

        $tag_with_link = "<a href='{$verificationUrl}' target='_blank' style='display: inline-block;padding: 12px 25px;background-color: #4CAF50;color: white;text-decoration: none;border-radius: 5px;font-weight: bold;margin-top: 15px;'>Verify</a>";

        Notification::route('mail', $this->email)->notifyNow(new UserAutoNotification(
            NotificationType::VERIFY_EMAIL,
            [
                'verify_button' => $tag_with_link,
                'verification_link' => $verificationUrl,
            ]
        ));
    }

    public function sendEmailOptVerificationNotification()
    {
        if (! $this->sendEmailOtp($this->email)) {
            throw new CustomWebException('Failed to send Email');
        }
    }

    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->save();
    }
}
