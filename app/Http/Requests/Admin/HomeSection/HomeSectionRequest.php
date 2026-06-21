<?php

namespace App\Http\Requests\Admin\HomeSection;

use App\Enums\HomeSectionLayoutEnum;
use App\Enums\HomeSectionSourceEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomeSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'source' => ['required', Rule::in(HomeSectionSourceEnum::values())],
            'layout' => ['required', Rule::in(HomeSectionLayoutEnum::values())],

            // Only relevant for the matching source — required there, ignored otherwise.
            'category_id' => ['nullable', 'required_if:source,category', 'exists:categories,id'],
            'product_ids' => [
                'nullable',
                'array',
                // Required for a custom list UNLESS the admin opted into the latest-products fallback.
                Rule::requiredIf(fn () => $this->input('source') === 'products' && ! $this->boolean('fallback_latest')),
            ],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'fallback_latest' => ['boolean'],

            'product_limit' => ['required', 'integer', 'min:1', 'max:48'],
            'view_all_url' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required_if' => __('Please choose a category for this section.'),
            'product_ids.required_if' => __('Please pick at least one product for this section.'),
        ];
    }

    /**
     * Build the persistable payload, nulling out fields that don't apply
     * to the chosen source so stale links can never resurface.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = $this->validated();
        $source = HomeSectionSourceEnum::from($data['source']);

        return [
            'title' => trim($data['title'] ?? '') ?: null,
            'subtitle' => trim($data['subtitle'] ?? '') ?: null,
            'source' => $data['source'],
            'layout' => $data['layout'],
            // Category is required for the Category source and an optional
            // filter for the Custom Product List source; irrelevant otherwise.
            'category_id' => ($source->needsCategory() || $source->needsProducts())
                ? ($data['category_id'] ?? null)
                : null,
            'product_ids' => $source->needsProducts()
                ? array_values(array_map('intval', $data['product_ids'] ?? []))
                : null,
            'fallback_latest' => $source->needsProducts() && (bool) ($data['fallback_latest'] ?? false),
            'product_limit' => (int) $data['product_limit'],
            'view_all_url' => trim($data['view_all_url'] ?? '') ?: null,
            'is_active' => (bool) ($data['is_active'] ?? false),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
