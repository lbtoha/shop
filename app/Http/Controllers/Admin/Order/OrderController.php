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
                'label' => __('Create Order'),
                'icon' => 'ph ph-plus',
                'type' => 'link',
                'link' => route('admin.orders.create'),
            ],
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
     * Show the manual order-entry form (e.g. for WhatsApp/phone orders).
     */
    public function create()
    {
        adminUserHasPermission(permission: 'create');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-left',
                'type' => 'link',
                'link' => route('admin.orders.index'),
            ],
        ];

        return view('admin.pages.orders.create', compact('buttons'));
    }

    /**
     * JSON product search for the order-entry product picker.
     */
    public function productSearch(Request $request)
    {
        adminUserHasPermission(permission: 'create');

        $term = trim((string) $request->get('q', ''));

        $products = \App\Models\Product::query()
            ->active()
            ->with('variants')
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->limit(20)
            ->get();

        $results = $products->map(function (\App\Models\Product $product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'stock' => (int) $product->stock,
                'has_variants' => $product->variants->isNotEmpty(),
                'variants' => $product->variants->map(fn ($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'price' => $v->price(),
                    'stock' => (int) $v->stock,
                ])->values(),
            ];
        });

        return response()->json(['data' => $results]);
    }

    /**
     * Persist a manually-entered order, then redirect to its detail page.
     */
    public function store(Request $request, \App\Services\Ecommerce\ManualOrderService $manualOrder)
    {
        adminUserHasPermission(permission: 'create');

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:30',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string|max:1000',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:30',
            'note' => 'nullable|string|max:1000',
            'shipping_cost' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = $manualOrder->placeOrder(
                $validated,
                $validated['items'],
                (float) ($validated['shipping_cost'] ?? 0)
            );
        } catch (\App\Exceptions\CustomWebException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        \App\Services\Ecommerce\OrderNotifier::orderPlaced($order);

        return response()->json([
            'message' => __('Order :number created.', ['number' => $order->order_number]),
            'redirect' => route('admin.orders.show', $order->id),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        adminUserHasPermission(permission: 'read');

        // Eager-load variant so the view can display the selected option (e.g. Size 42).
        $order->load('items.product', 'items.variant');

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

            if ($statusChanged) {
                $oldStatus = $order->status->value;
                $newStatus = $validated['status'];

                if ($newStatus === OrderStatusEnum::CANCELLED->value) {
                    $this->restockOrder($order);
                } elseif ($oldStatus === OrderStatusEnum::CANCELLED->value) {
                    $this->deductOrder($order);
                }
            }

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

        try {
            DB::beginTransaction();

            // Advancing status from CANCELLED is not possible, so only need to handle normal pipeline progression.
            $order->update(['status' => $next->value]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        \App\Services\Ecommerce\OrderNotifier::statusUpdated($order->fresh());

        return response()->json([
            'message' => __('Order moved to :status', ['status' => __($next->label())]),
            'redirect' => route('admin.orders.show', $order->id),
        ]);
    }

    /**
     * Create a Steadfast courier consignment for the order.
     */
    public function sendToSteadfast(Order $order)
    {
        adminUserHasPermission(permission: 'edit');
        protectOnDemo($order);

        try {
            \App\Services\Ecommerce\SteadfastService::createConsignment($order);
        } catch (\App\Exceptions\CustomWebException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => __('Consignment created on Steadfast.'),
            'redirect' => route('admin.orders.show', $order->id),
        ]);
    }

    /**
     * Refresh the Steadfast delivery status for the order.
     */
    public function refreshSteadfastStatus(Order $order)
    {
        adminUserHasPermission(permission: 'edit');

        if (! $order->courier_consignment_id) {
            return response()->json(['message' => __('This order has no Steadfast consignment.')], 422);
        }

        \App\Services\Ecommerce\SteadfastService::refreshStatus($order);

        return response()->json([
            'message' => __('Delivery status refreshed.'),
            'redirect' => route('admin.orders.show', $order->id),
        ]);
    }

    /**
     * Download a PDF invoice for the order.
     */
    public function invoice(Order $order)
    {
        adminUserHasPermission(permission: 'read');

        $order->load(['items.product', 'items.variant']);

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

        try {
            DB::beginTransaction();

            if ($order->status !== OrderStatusEnum::CANCELLED) {
                $this->restockOrder($order);
            }

            $order->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json(['message' => __('Order deleted successfully')]);
    }

    /**
     * Restore stock to products/variants when an order is cancelled or deleted.
     */
    private function restockOrder(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if ($item->variant_id) {
                $variant = \App\Models\ProductVariant::find($item->variant_id);
                if ($variant) {
                    $variant->increment('stock', $item->quantity);
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            } else {
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        if ($order->coupon_id) {
            $coupon = \App\Models\Coupon::find($order->coupon_id);
            if ($coupon && $coupon->used_count > 0) {
                $coupon->decrement('used_count');
            }
        }
    }

    /**
     * Deduct stock from products/variants when a cancelled order is reactivated.
     */
    private function deductOrder(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if ($item->variant_id) {
                $variant = \App\Models\ProductVariant::find($item->variant_id);
                if (! $variant) {
                    throw new \Exception(__('Product variant is no longer available.'));
                }
                if ($variant->stock < $item->quantity) {
                    throw new \Exception(__(':product (:variant) is out of stock.', ['product' => $item->product_name, 'variant' => $variant->name]));
                }
                $variant->decrement('stock', $item->quantity);
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    $product->decrement('stock', $item->quantity);
                }
            } else {
                $product = \App\Models\Product::find($item->product_id);
                if (! $product) {
                    throw new \Exception(__('Product is no longer available.'));
                }
                if ($product->stock < $item->quantity) {
                    throw new \Exception(__(':product is out of stock.', ['product' => $product->name]));
                }
                $product->decrement('stock', $item->quantity);
            }
        }

        if ($order->coupon_id) {
            $coupon = \App\Models\Coupon::find($order->coupon_id);
            if ($coupon) {
                $coupon->increment('used_count');
            }
        }
    }
}
