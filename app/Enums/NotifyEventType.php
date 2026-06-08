<?php

namespace App\Enums;

enum NotifyEventType: string
{
    case NEW_USER = 'new_user';
    case WITHDRAW = 'withdraw';
    case DEPOSIT = 'deposit';
    case AUTO = 'auto';
    case SCHEDULE = 'schedule';

    case BONUS = 'bonus';

    public function route(int $id): string
    {
        return match ($this) {
            self::NEW_USER => route('admin.users.edit', $id),
            default => route('admin.dashboard'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
