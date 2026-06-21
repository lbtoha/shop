<?php

namespace App\Enums;

/**
 * Where a home section pulls its products from.
 */
enum HomeSectionSourceEnum: string
{
    case CATEGORY = 'category';   // all products in a chosen category
    case PRODUCTS = 'products';   // a hand-picked custom product list
    case FEATURED = 'featured';   // products flagged is_featured
    case LATEST = 'latest';       // newest products

    public function label(): string
    {
        return match ($this) {
            self::CATEGORY => 'Category Products',
            self::PRODUCTS => 'Custom Product List',
            self::FEATURED => 'Featured Products',
            self::LATEST => 'Latest Products',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CATEGORY => 'info',
            self::PRODUCTS => 'primary',
            self::FEATURED => 'warning',
            self::LATEST => 'success',
        };
    }

    /** Does this source require a category to be chosen? */
    public function needsCategory(): bool
    {
        return $this === self::CATEGORY;
    }

    /** Does this source require a hand-picked product list? */
    public function needsProducts(): bool
    {
        return $this === self::PRODUCTS;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<int, array{value:string, label:string}> */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
