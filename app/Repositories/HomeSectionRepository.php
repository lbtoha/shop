<?php

namespace App\Repositories;

use App\Models\HomeSection;
use App\Services\Ecommerce\HomeSectionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Single access point for home-section data.
 *
 * Admin reads/writes go straight to the model; the storefront reads the
 * fully-resolved, cached payload so the home page costs a single cache hit
 * instead of a query per section on every request.
 */
class HomeSectionRepository
{
    public const CACHE_KEY = 'home_sections_resolved';

    public function __construct(private readonly HomeSectionService $service) {}

    /**
     * All sections for the admin list, in display order.
     *
     * @return Collection<int, HomeSection>
     */
    public function allOrdered(): Collection
    {
        return HomeSection::with('category')->ordered()->get();
    }

    /**
     * Resolved, renderable sections for the storefront home page (cached).
     *
     * @return array<int, array{id:int, title:string, eyebrow:string, layout:string, products:Collection, viewAll:?string}>
     */
    public function forStorefront(): array
    {
        return Cache::remember(self::CACHE_KEY, $this->cacheTtl(), function () {
            $sections = HomeSection::with('category')->active()->ordered()->get();

            return $this->service->resolveAll($sections);
        });
    }

    /**
     * Persist a new display order from an ordered list of section ids.
     *
     * @param  array<int, int|string>  $orderedIds
     */
    public function reorder(array $orderedIds): void
    {
        foreach (array_values($orderedIds) as $position => $id) {
            HomeSection::whereKey($id)->update(['sort_order' => $position + 1]);
        }

        $this->flushCache();
    }

    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function cacheTtl(): int
    {
        return (int) config('extra_service.site_pagination_config.cache_time', 600);
    }
}
