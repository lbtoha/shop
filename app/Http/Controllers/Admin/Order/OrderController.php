<?php

namespace App\Http\Controllers\Admin\Order;

use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ModalIndexQuey;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        // KPI summary cards
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', OrderStatusEnum::PENDING->value)->count(),
            'today' => Order::whereDate('created_at', today())->count(),
            'revenue' => (float) Order::where('status', '!=', OrderStatusEnum::CANCELLED->value)->sum('total'),
        ];

        $buttons = [
            [
                'label' => __('Export CSV'),
                'icon' => 'ph ph-download-simple',
                'type' => 'link',
                'link' => route('admin.orders.export', request()->only('status')),
            ],
        ];

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
                'render' => function ($order) {
                    $status = $order->payment_status;
                    if ($status instanceof OrderPaymentStatusEnum) {
                        return '<span class="status '.$status->color().' capitalize">'.__($status->label()).'</span>';
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
                        return '<span class="status '.$status->color().' capitalize">'.__($status->label()).'</span>';
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
                            'label' => __('Invoice'),
                            'icon' => 'ph ph-file-pdf',
                            'type' => 'link',
                            'href' => route('admin.orders.invoice', $order->id),
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

        return view('admin.pages.orders.index', compact('tab_buttons', 'orders', 'columns', 'stats', 'buttons'));
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
            'status' => ['required', 'in:'.implode(',', OrderStatusEnum::values())],
            'payment_status' => ['required', 'in:'.implode(',', OrderPaymentStatusEnum::values())],
        ]);

        $statusChanged = $order->status->value !== $validated['status'];

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
     * Advance the order to the next status in the fulfilment pipeline (one click).
     */
    public function advanceStatus(Order $order)
    {
        adminUserHasPermission(permission: 'edit');
        protectOnDemo($order);

        $next = $order->status->next();

        if (! $next) {
            return response()->json(['message' => __('Order is already at the final status.')], 422);
        }

        $order->update(['status' => $next->value]);

        \App\Services\Ecommerce\OrderNotifier::statusUpdated($order->fresh());

        return response()->json([
            'message' => __('Order moved to :status', ['status' => __($next->label())]),
            'redirect' => route('admin.orders.show', $order->id),
        ]);
    }

    /**
     * Download a PDF invoice for the order.
     */
    public function invoice(Order $order)
    {
        adminUserHasPermission(permission: 'read');

        $order->load('items');

        $pdf = Pdf::loadView('admin.pages.orders.invoice', compact('order'));

        return $pdf->download('invoice-'.$order->order_number.'.pdf');
    }

    /**
     * Export the (filtered) order list as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        adminUserHasPermission(permission: 'read');

        $orders = Order::query()
            ->withCount('items')
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->get();

        $filename = 'orders-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order #', 'Customer', 'Phone', 'Email', 'Items', 'Subtotal', 'Shipping', 'Total', 'Payment', 'Status', 'Placed']);
            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_phone,
                    $order->customer_email,
                    $order->items_count,
                    $order->subtotal,
                    $order->shipping_cost,
                    $order->total,
                    $order->payment_status?->label(),
                    $order->status?->label(),
                    $order->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
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
