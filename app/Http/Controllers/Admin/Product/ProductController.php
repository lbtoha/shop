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

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'low' => Product::whereBetween('stock', [1, 5])->count(),
            'out' => Product::where('stock', 0)->count(),
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
                        ? '<button type="button" class="action-confirm-btn status bg-green-100 text-green-600 hover:opacity-80 transition-opacity" action="'.route('admin.products.toggle-status', $product->id).'" method="POST" title="'.__('Toggle Status?').'" text="'.__('Change status of :name to Inactive?', ['name' => $product->name]).'">'.__('Active').'</button>'
                        : '<button type="button" class="action-confirm-btn status bg-gray-100 text-gray-500 hover:opacity-80 transition-opacity" action="'.route('admin.products.toggle-status', $product->id).'" method="POST" title="'.__('Toggle Status?').'" text="'.__('Change status of :name to Active?', ['name' => $product->name]).'">'.__('Inactive').'</button>';
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
                            'label' => $product->is_active ? __('Deactivate') : __('Activate'),
                            'icon' => $product->is_active ? 'ph ph-toggle-left' : 'ph ph-toggle-right',
                            'type' => 'action-confirm',
                            'href' => route('admin.products.toggle-status', $product->id),
                            'method' => 'POST',
                            'title' => __('Toggle Product Status?'),
                            'text' => __('Change status of :name to :status?', ['name' => $product->name, 'status' => $product->is_active ? __('Inactive') : __('Active')]),
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

        return view('admin.pages.products.index', compact('buttons', 'products', 'columns', 'stats'));
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
        $variants = $validated['variants'] ?? [];
        unset($validated['images'], $validated['variants']);

        DB::transaction(function () use ($validated, $images, $variants) {
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

            $this->syncVariants($product, $variants);
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

        $product->load('images', 'variants');
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
        $variants = $validated['variants'] ?? [];
        unset($validated['images'], $validated['variants']);

        DB::transaction(function () use ($validated, $images, $variants, $product) {
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

            $this->syncVariants($product, $variants);
        });

        return response()->json(['message' => __('Product updated successfully'), 'redirect' => route('admin.products.index')]);
    }

    /**
     * Replace a product's variants from the submitted rows. Rows with neither a
     * color nor a size are skipped. Delete-and-recreate keeps it simple; existing
     * order_items keep their snapshot (variant_id is null-on-delete).
     *
     * @param  array<int, array<string, mixed>>  $variants
     */
    private function syncVariants(Product $product, array $variants): void
    {
        $product->variants()->delete();

        $sort = 0;
        foreach ($variants as $row) {
            $color = trim((string) ($row['color'] ?? ''));
            $size = trim((string) ($row['size'] ?? ''));

            if ($color === '' && $size === '') {
                continue;
            }

            $attributes = [];
            if ($color !== '') {
                $attributes['Color'] = $color;
            }
            if ($size !== '') {
                $attributes['Size'] = $size;
            }

            $product->variants()->create([
                'name' => implode(' / ', array_values($attributes)),
                'sku' => $row['sku'] ?? null,
                'attributes' => $attributes,
                'price_adjustment' => $row['price_adjustment'] ?? 0,
                'stock' => $row['stock'] ?? 0,
                'sort_order' => $sort++,
            ]);
        }
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

    /**
     * Toggle the active status of the product.
     */
    public function toggleStatus(Product $product)
    {
        adminUserHasPermission(permission: 'edit');

        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return response()->json([
            'message' => __('Product status updated successfully'),
            'reload' => true,
        ]);
    }
}
