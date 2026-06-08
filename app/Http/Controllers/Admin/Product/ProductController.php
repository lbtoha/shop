<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ModalIndexQuey;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Create New Product'),
                'icon' => 'ph ph-plus',
                'type' => 'link',
                'link' => route('admin.products.create'),
            ],
        ];

        $products = ModalIndexQuey::get(Product::query(), ['category']);

        $columns = [
            [
                'label' => __('Image'),
                'render' => function ($product) {
                    $src = $product->thumbnail ?: asset('admin/images/placeholder.png');

                    return '<img src="'.$src.'" width="48" height="48" class="rounded object-cover w-12 h-12" alt="'.e($product->name).'" />';
                },
            ],
            [
                'label' => __('Name'),
                'key' => 'name',
                'header_class' => 'lg:w-[300px]',
                'render' => function ($product) {
                    return '<a href="'.route('admin.products.edit', $product->id).'" class="s-text font-medium hover:text-primary">'.e($product->name).'</a>';
                },
            ],
            [
                'label' => __('Category'),
                'render' => function ($product) {
                    return '<span class="s-text">'.e($product->category?->name ?? '—').'</span>';
                },
            ],
            [
                'label' => __('Price'),
                'key' => 'price',
                'is_sortable' => true,
                'render' => function ($product) {
                    return '<span class="s-text font-medium">'.amountWithSymbol($product->price).'</span>';
                },
            ],
            [
                'label' => __('Stock'),
                'key' => 'stock',
                'is_sortable' => true,
                'render' => function ($product) {
                    if ($product->stock <= 0) {
                        return '<span class="status bg-red-100 text-red-600">'.__('Out of stock').'</span>';
                    }
                    if ($product->stock <= 5) {
                        return '<span class="status bg-yellow-100 text-yellow-600">'.$product->stock.'</span>';
                    }

                    return '<span class="s-text font-medium">'.$product->stock.'</span>';
                },
            ],
            [
                'label' => __('Active'),
                'render' => function ($product) {
                    return $product->is_active
                        ? '<span class="status bg-green-100 text-green-600">'.__('Active').'</span>'
                        : '<span class="status bg-gray-100 text-gray-500">'.__('Inactive').'</span>';
                },
            ],
            [
                'label' => __('Featured'),
                'render' => function ($product) {
                    return $product->is_featured
                        ? '<span class="status bg-blue-100 text-blue-600">'.__('Featured').'</span>'
                        : '<span class="status bg-gray-100 text-gray-500">'.__('No').'</span>';
                },
            ],
            [
                'label' => __('Action'),
                'header_class' => 'flex justify-end',
                'render' => function ($product) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'type' => 'link',
                            'href' => route('admin.products.edit', $product->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.products.destroy', $product->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.products.index', compact('buttons', 'products', 'columns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        adminUserHasPermission(permission: 'create');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.products.index'),
            ],
        ];

        $categories = Category::active()->get();

        return view('admin.pages.products.create', compact('buttons', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        adminUserHasPermission(permission: 'create');

        $validated = $request->validated();
        $images = $validated['images'] ?? [];
        unset($validated['images']);

        DB::transaction(function () use ($validated, $images) {
            $product = Product::create($validated);

            // Gallery: create a ProductImage row for each non-empty submitted path, keeping the order index as sort_order.
            foreach ($images as $index => $path) {
                if (! empty($path)) {
                    $product->images()->create([
                        'image' => $path,
                        'sort_order' => $index,
                    ]);
                }
            }
        });

        return response()->json(['message' => __('Product created successfully'), 'redirect' => route('admin.products.index')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.products.index'),
            ],
        ];

        $product->load('images');
        $categories = Category::active()->get();

        return view('admin.pages.products.edit', compact('buttons', 'product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validated();
        $images = $validated['images'] ?? [];
        unset($validated['images']);

        DB::transaction(function () use ($validated, $images, $product) {
            $product->update($validated);

            // Sync gallery: simplest correct approach — delete all existing images and recreate
            // from the submitted images[] array so removed slots are dropped and order is preserved.
            $product->images()->delete();

            foreach ($images as $index => $path) {
                if (! empty($path)) {
                    $product->images()->create([
                        'image' => $path,
                        'sort_order' => $index,
                    ]);
                }
            }
        });

        return response()->json(['message' => __('Product updated successfully'), 'redirect' => route('admin.products.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        adminUserHasPermission(permission: 'delete');

        // FK constraints: product_images cascade on delete, order_items are null on delete.
        $product->delete();

        return response()->json(['message' => __('Product deleted successfully'), 'reload' => true]);
    }
}
