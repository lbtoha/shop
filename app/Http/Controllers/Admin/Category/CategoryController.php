<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\CategoryRequest;
use App\Models\Category;
use App\Services\ModalIndexQuey;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Create New Category'),
                'icon' => 'ph ph-plus',
                'type' => 'link',
                'link' => route('admin.categories.create'),
            ],
        ];

        $categories = ModalIndexQuey::get(Category::query()->with('parent'));

        $columns = [
            [
                'label' => __('Image'),
                'render' => function ($category) {
                    if ($category->image) {
                        return '<img src="'.$category->image.'" width="40" height="40" class="rounded object-cover w-10 h-10" alt="'.e($category->name).'" />';
                    }

                    return '<span class="text-gray-400">—</span>';
                },
            ],
            [
                'label' => __('Name'),
                'key' => 'name',
                'header_class' => 'lg:w-[300px]',
                'render' => function ($category) {
                    return '<p class="s-text font-medium">'.e($category->name).'</p>';
                },
            ],
            [
                'label' => __('Parent'),
                'render' => function ($category) {
                    return '<span class="s-text">'.e($category->parent?->name ?? '—').'</span>';
                },
            ],
            [
                'label' => __('Products'),
                'render' => function ($category) {
                    return '<span class="s-text">'.$category->products()->count().'</span>';
                },
            ],
            [
                'label' => __('Active'),
                'key' => 'is_active',
                'is_sortable' => true,
                'render' => function ($category) {
                    return $category->is_active
                        ? '<span class="status success capitalize">'.__('Active').'</span>'
                        : '<span class="status danger capitalize">'.__('Inactive').'</span>';
                },
            ],
            [
                'label' => __('Action'),
                'header_class' => 'flex justify-end',
                'render' => function ($category) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'type' => 'link',
                            'href' => route('admin.categories.edit', $category->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.categories.destroy', $category->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.categories.index', compact('buttons', 'categories', 'columns'));
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
                'link' => route('admin.categories.index'),
            ],
        ];

        $categories = Category::orderBy('name')->get();

        return view('admin.pages.categories.create', compact('buttons', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        adminUserHasPermission(permission: 'create');

        try {
            DB::beginTransaction();

            Category::create($request->validated());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json([
            'message' => __('Category created successfully'),
            'redirect' => route('admin.categories.index'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.categories.index'),
            ],
        ];

        $categories = Category::where('id', '!=', $category->id)->orderBy('name')->get();

        return view('admin.pages.categories.edit', compact('buttons', 'category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        adminUserHasPermission(permission: 'edit');

        try {
            DB::beginTransaction();

            $category->update($request->validated());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json([
            'message' => __('Category updated successfully'),
            'redirect' => route('admin.categories.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        adminUserHasPermission(permission: 'delete');

        $category->delete();

        return response()->json(['message' => __('Category deleted successfully')]);
    }
}
