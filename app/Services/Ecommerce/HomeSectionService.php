<?php

namespace App\Services\Ecommerce;

use App\Enums\HomeSectionSourceEnum;
use App\Models\HomeSection;
use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Resolves admin-configured home sections into renderable payloads.
 *
 * Every product query goes through Product::active()->inStock() so that
 * inactive / out-of-stock products (and products under disabled categories)
 * can never leak onto the storefront, regardless of admin configuration.
 */
class HomeSectionService
{
    private const MIN_LIMIT = 1;

    private const MAX_LIMIT = 48;

    /**
     * Resolve a list of sections, dropping any that end up with no products.
     *
     * @param  Collection<int, HomeSection>  $sections
     * @return array<int, array{id:int, title:string, eyebrow:string, layout:string, products:Collection, viewAll:?string}>
     */
    public function resolveAll(Collection $sections): array
    {
        return $sections
            ->map(fn (HomeSection $section) => $this->resolve($section))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Resolve a single section, or null when it has nothing valid to show.
     *
     * @return array{id:int, title:string, eyebrow:string, layout:string, products:Collection, viewAll:?string}|null
     */
    public function resolve(HomeSection $section): ?array
    {
        $products = $this->products($section);

        if ($products->isEmpty()) {
            return null;
        }

        return [
            'id' => $section->id,
            'title' => $this->title($section),
            'eyebrow' => $this->eyebrow($section),
            'layout' => $section->layout->value,
            'products' => $products,
            'viewAll' => $this->viewAll($section),
        ];
    }

    private function products(HomeSection $section): Collection
    {
        $limit = $this->limit($section);

        return match ($section->source) {
            HomeSectionSourceEnum::CATEGORY => $this->categoryProducts($section, $limit),
            HomeSectionSourceEnum::PRODUCTS => $this->customProducts($section, $limit),
            HomeSectionSourceEnum::FEATURED => $this->baseQuery()->where('is_featured', true)->latest()->take($limit)->get(),
            HomeSectionSourceEnum::LATEST => $this->latestProducts($limit),
        };
    }

    /**
     * Base product query shared by every source: only active, in-stock
     * products (and never under a disabled category), eager-loaded with
     * everything the product card touches so the cached payload is
     * self-contained and never lazy-loads on render.
     */
    private function baseQuery()
    {
        return Product::active()->inStock()
            ->with(['category', 'images'])
            ->withCount('variants');
    }

    private function latestProducts(int $limit): Collection
    {
        return $this->baseQuery()->latest()->take($limit)->get();
    }

    private function categoryProducts(HomeSection $section, int $limit): Collection
    {
        // Guard against a section pointing at a deleted / disabled category.
        $category = $section->category;
        if (! $category || ! $category->is_active) {
            return collect();
        }

        return $this->baseQuery()
            ->where('category_id', $category->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    private function customProducts(HomeSection $section, int $limit): Collection
    {
        $categoryId = $section->category_id;

        // When a category filter is set but the category is gone / disabled,
        // the section shows nothing (rather than leaking other categories).
        if ($categoryId && (! $section->category || ! $section->category->is_active)) {
            return collect();
        }

        $ids = collect($section->product_ids ?? [])->filter()->values();

        $products = collect();

        if ($ids->isNotEmpty()) {
            // Preserve the admin-defined order of the hand-picked list. Inactive /
            // out-of-stock picks are dropped automatically by baseQuery().
            $ordered = implode(',', $ids->map(fn ($id) => (int) $id)->all());

            $query = $this->baseQuery()
                ->whereIn('id', $ids)
                ->orderByRaw("FIELD(id, {$ordered})");

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            $products = $query->take($limit)->get();
        }

        // Optional fallback so the section never disappears when the manual
        // selection is empty (or every pick got filtered out) — still honouring
        // the category filter when one is set.
        if ($products->isEmpty() && $section->fallback_latest) {
            $fallback = $this->baseQuery()->latest();

            if ($categoryId) {
                $fallback->where('category_id', $categoryId);
            }

            return $fallback->take($limit)->get();
        }

        return $products;
    }

    private function limit(HomeSection $section): int
    {
        return max(self::MIN_LIMIT, min((int) $section->product_limit, self::MAX_LIMIT));
    }

    private function title(HomeSection $section): string
    {
        if (filled($section->title)) {
            return $section->title;
        }

        if ($section->source === HomeSectionSourceEnum::CATEGORY && $section->category) {
            return $section->category->name;
        }

        return $section->source->label();
    }

    private function eyebrow(HomeSection $section): string
    {
        return filled($section->subtitle) ? $section->subtitle : __('Collection');
    }

    private function viewAll(HomeSection $section): ?string
    {
        if (filled($section->view_all_url)) {
            return $section->view_all_url;
        }

        // Category source, or a custom list filtered to one category, links to it.
        if (in_array($section->source, [HomeSectionSourceEnum::CATEGORY, HomeSectionSourceEnum::PRODUCTS], true)
            && $section->category) {
            return route('shop.index', ['category' => $section->category->slug]);
        }

        if ($section->source === HomeSectionSourceEnum::FEATURED) {
            return route('shop.index', ['featured' => 1]);
        }

        if ($section->source === HomeSectionSourceEnum::LATEST) {
            return route('shop.index');
        }

        return null;
    }
}
