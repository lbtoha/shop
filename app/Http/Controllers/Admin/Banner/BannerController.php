<?php

namespace App\Http\Controllers\Admin\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banner\BannerRequest;
use App\Models\Banner;
use App\Services\ModalIndexQuey;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Create New Banner'),
                'icon' => 'ph ph-plus',
                'type' => 'link',
                'link' => route('admin.banners.create'),
            ],
        ];

        $banners = ModalIndexQuey::get(Banner::query()->orderBy('sort_order'));

        $columns = [
            [
                'label' => __('Image'),
                'render' => function ($banner) {
                    if ($banner->image) {
                        return '<img src="'.$banner->image.'" width="80" height="40" class="rounded object-cover w-20 h-10" alt="'.e($banner->title).'" />';
                    }

                    return '<span class="text-gray-400">—</span>';
                },
            ],
            [
                'label' => __('Title'),
                'key' => 'title',
                'render' => function ($banner) {
                    return '<p class="s-text font-medium">'.e($banner->title ?? '—').'</p>'
                        .($banner->subtitle ? '<span class="text-xs text-gray-400">'.e($banner->subtitle).'</span>' : '');
                },
            ],
            [
                'label' => __('Order'),
                'key' => 'sort_order',
                'is_sortable' => true,
            ],
            [
                'label' => __('Active'),
                'key' => 'is_active',
                'is_sortable' => true,
                'render' => function ($banner) {
                    return $banner->is_active
                        ? '<span class="status success capitalize">'.__('Active').'</span>'
                        : '<span class="status danger capitalize">'.__('Inactive').'</span>';
                },
            ],
            [
                'label' => __('Action'),
                'header_class' => 'flex justify-end',
                'render' => function ($banner) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'type' => 'link',
                            'href' => route('admin.banners.edit', $banner->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.banners.destroy', $banner->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.banners.index', compact('buttons', 'banners', 'columns'));
    }

    public function create()
    {
        adminUserHasPermission(permission: 'create');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.banners.index'),
            ],
        ];

        return view('admin.pages.banners.create', compact('buttons'));
    }

    public function store(BannerRequest $request)
    {
        adminUserHasPermission(permission: 'create');

        try {
            DB::beginTransaction();

            Banner::create($request->validated());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json([
            'message' => __('Banner created successfully'),
            'redirect' => route('admin.banners.index'),
        ]);
    }

    public function edit(Banner $banner)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.banners.index'),
            ],
        ];

        return view('admin.pages.banners.edit', compact('buttons', 'banner'));
    }

    public function update(BannerRequest $request, Banner $banner)
    {
        adminUserHasPermission(permission: 'edit');

        try {
            DB::beginTransaction();

            $banner->update($request->validated());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json([
            'message' => __('Banner updated successfully'),
            'redirect' => route('admin.banners.index'),
        ]);
    }

    public function destroy(Banner $banner)
    {
        adminUserHasPermission(permission: 'delete');

        $banner->delete();

        return response()->json(['message' => __('Banner deleted successfully')]);
    }
}
