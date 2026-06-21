<?php

namespace App\Enums;

/**
 * How a home section is rendered on the storefront.
 */
enum HomeSectionLayoutEnum: string
{
    case GRID = 'grid';         // responsive product grid
    case SLIDER = 'slider';     // swipeable row with arrows
    case CAROUSEL = 'carousel'; // auto-playing, looping slider
    case BANNER = 'banner';     // tinted promotional band wrapping the products

    public function label(): string
    {
        return match ($this) {
            self::GRID => 'Grid',
            self::SLIDER => 'Slider',
            self::CAROUSEL => 'Carousel',
            self::BANNER => 'Banner',
        };
    }

    /**
     * The shop partial used to render this layout.
     * Lives in resources/views/shop/partials/.
     */
    public function partial(): string
    {
        return match ($this) {
            self::GRID => 'shop.partials.product-section',
            self::SLIDER => 'shop.partials.product-slider-section',
            self::CAROUSEL => 'shop.partials.product-carousel-section',
            self::BANNER => 'shop.partials.product-banner-section',
        };
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
