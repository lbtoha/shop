<?php

namespace App\Http\Controllers\Admin\Order;

use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ModalIndexQuey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $tab_buttons = [
            [
                'label' => __('All Orders'),
                'link' => route('admin.orders.index'),
            ],
        ];

        foreach (OrderStatusEnum::cases() as $case) {
            $tab_buttons[] = [
                'label' => __($case->label()),
                'count' => Order::where('status', $case->value)->count(),
                'link' => route('admin.orders.index', ['status' => $case->value]),
            ];
        }

        $orders = ModalIndexQuey::get(
            Order::query()
                ->withCount('items')
                ->when(request('status'), fn ($q, $s) => $q->where('status', $s)),
            ['items']
        );

        $columns = [
            [
                'label' => __('Order #'),
                'key' => 'order_number',
                'render' => function ($order) {
                    return '<a href="'.route('admin.orders.show', $order->id).'" class="s-text font-medium text-primary">#'.e($order->order_number).'</a>';
                },
            ],
            [
                'label' => __('Customer'),
                'render' => function ($order) {
                    return '<p class="s-text font-medium">'.e($order->customer_name).'</p>
                            <span class="text-xs">'.e($order->customer_phone).'</span>';
                },
            ],
            [
                'label' => __('Items'),
                'render' => function ($order) {
                    return '<span class="s-text font-medium">'.($order->items_count ?? $order->items->count()).'</span>';
                },
            ],
            [
                'label' => __('Total'),
                'render' => function ($order) {
                    return '<span class="s-text font-medium">'.amountWithSymbol($order->total).'</span>';
                },
            ],
            [
                'label' => __('Payment'),
                'key' => 'payment_status',
                'render' => function ($order) {
                    $status = $order->payment_status;
                    if ($status instanceof OrderPaymentStatusEnum) {
                        $html = '<select class="inline-order-payment-select status '.$status->color().' border-0 rounded cursor-pointer capitalize font-semibold pr-7 text-xs focus:ring-0 focus:outline-none" data-action="'.route('admin.orders.update-status', $order->id).'">';
                        foreach (OrderPaymentStatusEnum::cases() as $case) {
                            $selected = $order->payment_status->value === $case->value ? ' selected' : '';
                            $html .= '<option value="'.$case->value.'"'.$selected.' class="text-neutral-900 bg-white">'.__($case->label()).'</option>';
                        }
                        $html .= '</select>';

                        return $html;
                    }

                    return '<span class="status text-gray-400 capitalize">'.__('Unknown').'</span>';
                },
            ],
            [
                'label' => __('Status'),
                'key' => 'status',
                'render' => function ($order) {
                    $status = $order->status;
                    if ($status instanceof OrderStatusEnum) {
                        $html = '<select class="inline-order-status-select status '.$status->color().' border-0 rounded cursor-pointer capitalize font-semibold pr-7 text-xs focus:ring-0 focus:outline-none" data-action="'.route('admin.orders.update-status', $order->id).'">';
                        foreach (OrderStatusEnum::cases() as $case) {
                            $selected = $order->status->value === $case->value ? ' selected' : '';
                            $html .= '<option value="'.$case->value.'"'.$selected.' class="text-neutral-900 bg-white">'.__($case->label()).'</option>';
                        }
                        $html .= '</select>';

                        return $html;
                    }

                    return '<span class="status text-gray-400 capitalize">'.__('Unknown').'</span>';
                },
            ],
            [
                'label' => __('Placed'),
                'key' => 'created_at',
                'is_sortable' => true,
                'render' => function ($order) {
                    return '<p class="s-text font-medium">'.$order->created_at->format('Y-m-d H:i A').'</p>
                            <span class="text-xs">'.$order->created_at->diffForHumans().'</span>';
                },
            ],
            [
                'label' => __('Action'),
                'header_class' => 'flex justify-end',
                'render' => function ($order) {
                    $action_buttons = [
                        [
                            'label' => __('View'),
                            'icon' => 'ph ph-eye',
                            'type' => 'link',
                            'href' => route('admin.orders.show', $order->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.orders.destroy', $order->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.orders.index', compact('tab_buttons', 'orders', 'columns'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        adminUserHasPermission(permission: 'read');

        $order->load('items.product');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-left',
                'type' => 'link',
                'link' => route('admin.orders.index'),
            ],
        ];

        return view('admin.pages.orders.show', compact('order', 'buttons'));
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        adminUserHasPermission(permission: 'edit');
        protectOnDemo($order);

        $validated = $request->validate([
            'status' => ['sometimes', 'required', 'in:'.implode(',', OrderStatusEnum::values())],
            'payment_status' => ['sometimes', 'required', 'in:'.implode(',', OrderPaymentStatusEnum::values())],
        ]);

        $statusChanged = $request->has('status') && $order->status->value !== $validated['status'];

        try {
            DB::beginTransaction();

            $order->update($validated);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        if ($statusChanged) {
            \App\Services\Ecommerce\OrderNotifier::statusUpdated($order->fresh());
        }

        return response()->json([
            'message' => __('Order updated'),
            'redirect' => route('admin.orders.show', $order->id),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        adminUserHasPermission(permission: 'delete');
        protectOnDemo($order);

        $order->delete();

        return response()->json(['message' => __('Order deleted successfully')]);
    }
}
