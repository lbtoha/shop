<?php

namespace App\Enums;

enum OrderPaymentStatusEnum: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Unpaid',
            self::PAID => 'Paid',
            self::REFUNDED => 'Refunded',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'warning',
            self::PAID => 'success',
            self::REFUNDED => 'danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
