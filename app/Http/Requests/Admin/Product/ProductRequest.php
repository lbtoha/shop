<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // On update the route-model bound product is available so we can ignore self for unique sku.
        $product = $this->route('product');
        $productId = $product?->id;

        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'thumbnail' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'buying_price' => ['nullable', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'shipping_cost_dhaka' => ['required', 'numeric', 'min:0'],
            'shipping_cost_outside' => ['required', 'numeric', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'string'],

            'variants' => ['nullable', 'array'],
            'variants.*.attrs' => ['nullable', 'array'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price_adjustment' => ['nullable', 'numeric'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
