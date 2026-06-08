<?php

namespace App\Traits;

trait HasPhoneVerify
{
    use EmailAndPhoneOTPVerification;

    public function phoneVerified()
    {
        return $this->phone_verified_at !== null;
    }

    public function phoneNotVerified()
    {
        return $this->phone_verified_at === null;
    }

    public function markPhoneAsVerified()
    {
        $this->phone_verified_at = now();
        $this->save();
    }

    public function markPhoneAsNotVerified()
    {
        $this->phone_verified_at = null;
        $this->save();
    }

    public function sendPhoneOptVerificationNotification()
    {
        if (! $this->phone) {
            throw new \Exception('Phone number is required');
        }

        if (! $this->sendPhoneOtp($this->phone)) {
            throw new \Exception('Failed to send SMS');
        }
    }
}
