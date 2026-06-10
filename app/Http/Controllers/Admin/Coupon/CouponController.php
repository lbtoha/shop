<?php

namespace App\Http\Controllers\Admin\Coupon;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Coupon\CouponRequest;
use App\Models\Coupon;
use App\Services\ModalIndexQuey;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Create New Coupon'),
                'icon' => 'ph ph-plus',
                'type' => 'link',
                'link' => route('admin.coupons.create'),
            ],
        ];

        $coupons = ModalIndexQuey::get(Coupon::query());

        $columns = [
            [
                'label' => __('Code'),
                'key' => 'code',
                'render' => fn ($c) => '<span class="status info capitalize font-medium">'.e($c->code).'</span>',
            ],
            [
                'label' => __('Discount'),
                'render' => fn ($c) => '<span class="s-text font-medium">'.e($c->valueLabel()).'</span>',
            ],
            [
                'label' => __('Min. Order'),
                'render' => fn ($c) => '<span class="s-text">'.((float) $c->min_subtotal > 0 ? amountWithSymbol($c->min_subtotal) : '—').'</span>',
            ],
            [
                'label' => __('Usage'),
                'render' => fn ($c) => '<span class="s-text">'.$c->used_count.' / '.($c->usage_limit ?? '∞').'</span>',
            ],
            [
                'label' => __('Expires'),
                'render' => fn ($c) => '<span class="s-text">'.($c->expires_at ? $c->expires_at->format('M d, Y') : '—').'</span>',
            ],
            [
                'label' => __('Active'),
                'key' => 'is_active',
                'is_sortable' => true,
                'render' => fn ($c) => $c->is_active
                    ? '<span class="status success capitalize">'.__('Active').'</span>'
                    : '<span class="status danger capitalize">'.__('Inactive').'</span>',
            ],
            [
                'label' => __('Action'),
                'header_class' => 'flex justify-end',
                'render' => function ($c) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'type' => 'link',
                            'href' => route('admin.coupons.edit', $c->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.coupons.destroy', $c->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.coupons.index', compact('buttons', 'coupons', 'columns'));
    }

    public function create()
    {
        adminUserHasPermission(permission: 'create');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.coupons.index'),
            ],
        ];

        return view('admin.pages.coupons.create', compact('buttons'));
    }

    public function store(CouponRequest $request)
    {
        adminUserHasPermission(permission: 'create');

        try {
            DB::beginTransaction();

            Coupon::create($request->validated());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json([
            'message' => __('Coupon created successfully'),
            'redirect' => route('admin.coupons.index'),
        ]);
    }

    public function edit(Coupon $coupon)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.coupons.index'),
            ],
        ];

        return view('admin.pages.coupons.edit', compact('buttons', 'coupon'));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        adminUserHasPermission(permission: 'edit');

        try {
            DB::beginTransaction();

            $coupon->update($request->validated());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json([
            'message' => __('Coupon updated successfully'),
            'redirect' => route('admin.coupons.index'),
        ]);
    }

    public function destroy(Coupon $coupon)
    {
        adminUserHasPermission(permission: 'delete');

        $coupon->delete();

        return response()->json(['message' => __('Coupon deleted successfully')]);
    }
}
