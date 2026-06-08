<?php

namespace App\Enums;

enum NotificationType: string
{
    case DEFAULT = 'default';
    case PASSWORD_RESET = 'password_reset';
    case VERIFY_EMAIL = 'verify_email';
    case WELCOME_MESSAGE = 'welcome_message';
    case KYC_VERIFICATION_ACCEPTED = 'kyc_verification_accepted';
    case KYC_VERIFICATION_REJECTED = 'kyc_verification_rejected';
    case CONTEST_STARTED = 'contest_stated';
    case CONTEST_ENDED = 'contest_ended';
    case CONTEST_WINNER = 'contest_winner';
    case DEPOSIT_CREATED = 'deposit_created';
    case DEPOSIT_PAID = 'deposit_paid';
    case DEPOSIT_CONFIRMED = 'deposit_confirmed';
    case DEPOSIT_FAILED = 'deposit_failed';
    case BADGE_EARNED = 'badge_earned';
    case WITHDRAWAL_REQUEST_CREATED = 'withdrawal_request_created';
    case WITHDRAWAL_REQUEST_APPROVED = 'withdrawal_request_approved';
    case WITHDRAWAL_REQUEST_REJECTED = 'withdrawal_request_rejected';
    case COIN_ADD = 'coin_add';
    case COIN_SUBTRACT = 'coin_subtract';
    case REFERRAL_BONUS = 'referral_bonus';
    case ACCOUNT_DEACTIVATED = 'account_deactivated';
    case ACCOUNT_BANNED = 'account_banned';
    case ACCOUNT_ACTIVE = 'account_active';
    case ORDER_PLACED = 'order_placed';
    case ORDER_STATUS_UPDATED = 'order_status_updated';

    public function label(): string
    {
        return match ($this) {
            self::DEFAULT => 'Default',
            self::PASSWORD_RESET => 'Password Reset',
            self::VERIFY_EMAIL => 'Verify Email',
            self::WELCOME_MESSAGE => 'Welcome Message',
            self::KYC_VERIFICATION_ACCEPTED => 'KYC Verification Accepted',
            self::KYC_VERIFICATION_REJECTED => 'KYC Verification Rejected',
            self::CONTEST_STARTED => 'Contest Started',
            self::REFERRAL_BONUS => 'Referral Bonus',
            self::CONTEST_ENDED => 'Contest Ended',
            self::CONTEST_WINNER => 'Contest Winner',
            self::DEPOSIT_CREATED => 'Deposit Created',
            self::DEPOSIT_PAID => 'Deposit Paid',
            self::DEPOSIT_CONFIRMED => 'Deposit Confirmed',
            self::DEPOSIT_FAILED => 'Deposit Failed',
            self::BADGE_EARNED => 'Badge Earned',
            self::WITHDRAWAL_REQUEST_CREATED => 'Withdrawal Request Created',
            self::WITHDRAWAL_REQUEST_APPROVED => 'Withdrawal Request Approved',
            self::WITHDRAWAL_REQUEST_REJECTED => 'Withdrawal Request Rejected',
            self::COIN_ADD => 'Coin Add',
            self::COIN_SUBTRACT => 'Coin Subtract',
            self::ACCOUNT_DEACTIVATED => 'Account Deactivated',
            self::ACCOUNT_BANNED => 'Account Banned',
            self::ACCOUNT_ACTIVE => 'Account Active',
            self::ORDER_PLACED => 'Order Placed',
            self::ORDER_STATUS_UPDATED => 'Order Status Updated',
        };
    }

    public function shortcodes(): array
    {
        $full_name = ['{{full_name}}' => [
            'name' => 'full_name',
            'hint' => 'User full name',
        ]];
        $email = ['{{email}}' => [
            'name' => 'email',
            'hint' => 'User email',
        ]];
        $phone = ['{{phone}}' => [
            'name' => 'phone',
            'hint' => 'User phone',
        ]];
        $amount = ['{{amount}}' => [
            'name' => 'amount',
            'hint' => 'Amount',
        ]];
        $coins = ['{{coins}}' => [
            'name' => 'coins',
            'hint' => 'Coins',
        ]];
        $track = ['{{track}}' => [
            'name' => 'track',
            'hint' => 'Withdrawal tracking number',
        ]];

        return match ($this) {
            self::DEFAULT => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
            ],
            self::VERIFY_EMAIL => [
                '{{verification_link}}' => [
                    'name' => 'verification_link',
                    'hint' => 'Verification link',
                ],
                '{{verify_button}}' => [
                    'name' => 'verify_button',
                    'hint' => 'Verify button',
                ],
            ],
            self::WELCOME_MESSAGE => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
            ],
            self::KYC_VERIFICATION_ACCEPTED => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
            ],
            self::KYC_VERIFICATION_REJECTED => [
                ...$full_name,
                ...$email,
                ...$phone,
            ],
            self::DEPOSIT_CREATED => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
            ],
            self::CONTEST_STARTED => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$coins,
            ],
            self::CONTEST_ENDED => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$coins,
            ],
            self::CONTEST_WINNER => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$coins,
                ...$track,
            ],
            self::REFERRAL_BONUS => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$coins,
            ],
            self::DEPOSIT_PAID => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
                ...$track,
            ],
            self::DEPOSIT_CONFIRMED => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
                ...$track,
            ],
            self::DEPOSIT_FAILED => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
                ...$track,
            ],
            self::BADGE_EARNED => [
                ...$full_name,
                ...$email,
                ...$phone,
                '{{badge_name}}' => [
                    'name' => 'badge_name',
                    'hint' => 'Badge name',
                ],
            ],
            self::WITHDRAWAL_REQUEST_CREATED => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
                ...$track,
            ],
            self::WITHDRAWAL_REQUEST_APPROVED => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
                ...$track,
            ],
            self::WITHDRAWAL_REQUEST_REJECTED => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
                ...$track,
            ],
            self::COIN_ADD => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
            ],
            self::COIN_SUBTRACT => [
                ...$full_name,
                ...$email,
                ...$phone,
                ...$amount,
            ],
            self::ACCOUNT_DEACTIVATED => [
                ...$full_name,
                ...$email,
                ...$phone,
            ],
            self::ACCOUNT_BANNED => [
                ...$full_name,
                ...$email,
                ...$phone,
            ],
            self::ACCOUNT_ACTIVE => [
                ...$full_name,
                ...$email,
                ...$phone,
            ],
            self::PASSWORD_RESET => [
                '{{reset_link}}' => [
                    'name' => 'reset_link',
                    'hint' => 'Reset link',
                ],
            ],
            self::ORDER_PLACED => [
                ...$full_name,
                ...$email,
                ...$phone,
                '{{order_number}}' => ['name' => 'order_number', 'hint' => 'Order number'],
                '{{order_total}}' => ['name' => 'order_total', 'hint' => 'Order total amount'],
            ],
            self::ORDER_STATUS_UPDATED => [
                ...$full_name,
                ...$email,
                ...$phone,
                '{{order_number}}' => ['name' => 'order_number', 'hint' => 'Order number'],
                '{{order_status}}' => ['name' => 'order_status', 'hint' => 'New order status'],
            ],
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function keys(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function levels(): array
    {
        return array_column(self::cases(), 'label');
    }
}
