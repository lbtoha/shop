<?php

namespace App\Enums;

enum MenuLocationEnum: string
{
    case HEADER = 'header';

    case QUICK_LINKS = 'quick-links';

    case RESOURCES_MENU = 'resources-menu';

    public function label(): string
    {
        return match ($this) {
            self::HEADER => 'Header',
            self::QUICK_LINKS => 'Quick Links',
            self::RESOURCES_MENU => 'Resources Menu',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
