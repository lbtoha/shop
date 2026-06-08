<?php

namespace App\Traits;

trait VerifyEmailAndPhone
{
    public function hasVerifiedPhone()
    {
        return ! is_null($this->phone_verified_at);
    }

    public function markPhoneAsVerified()
    {
        $this->forceFill([
            'phone_verified_at' => now(),
        ])->save();
    }

    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        $this->forceFill([
            'email_verified_at' => now(),
        ])->save();
    }
}
