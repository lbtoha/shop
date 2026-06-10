@php
    $company = config('application_info.company_info');
    $address = config('application_info.address');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1a1a1a; font-size: 12px; margin: 0; }
        .wrap { padding: 32px; }
        .row { width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; }
        .brand { font-size: 22px; font-weight: bold; color: #088178; }
        .muted { color: #777; }
        .title { font-size: 18px; font-weight: bold; }
        .box { background: #f7f8fa; border-radius: 6px; padding: 12px; }
        .items th { text-align: left; border-bottom: 2px solid #e6e6e6; padding: 8px 6px; font-size: 11px; color: #555; text-transform: uppercase; }
        .items td { padding: 8px 6px; border-bottom: 1px solid #eee; }
        .right { text-align: right; }
        .totals td { padding: 4px 6px; }
        .grand { font-size: 14px; font-weight: bold; border-top: 2px solid #e6e6e6; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; background: #ebf7ee; color: #088178; font-size: 11px; }
    </style>
</head>
<body>
<div class="wrap">
    <table class="head">
        <tr>
            <td>
                <div class="brand">{{ $company['name'] ?? config('app.name') }}</div>
                <div class="muted">
                    {{ $address['address'] ?? '' }}<br>
                    {{ $company['phone'] ?? '' }} · {{ $company['email'] ?? '' }}
                </div>
            </td>
            <td class="right">
                <div class="title">INVOICE</div>
                <div>#{{ $order->order_number }}</div>
                <div class="muted">{{ $order->created_at->format('M d, Y g:i A') }}</div>
                <div style="margin-top:6px;"><span class="badge">{{ ucfirst($order->status?->label()) }}</span></div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 24px;">
        <tr>
            <td style="width:50%; vertical-align:top;">
                <div class="muted" style="margin-bottom:4px;">BILL TO</div>
                <div class="box">
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->customer_phone }}<br>
                    @if ($order->customer_email){{ $order->customer_email }}<br>@endif
                    {{ $order->shipping_address }}
                    @if ($order->city), {{ $order->city }}@endif
                    @if ($order->zip_code) - {{ $order->zip_code }}@endif
                </div>
            </td>
            <td style="width:8px;"></td>
            <td style="width:50%; vertical-align:top;">
                <div class="muted" style="margin-bottom:4px;">PAYMENT</div>
                <div class="box">
                    Method: Cash on Delivery<br>
                    Payment: {{ ucfirst($order->payment_status?->label()) }}<br>
                    @if ($order->note)Note: {{ $order->note }}@endif
                </div>
            </td>
        </tr>
    </table>

    <table class="items" style="margin-top: 24px;">
        <thead>
            <tr>
                <th>Product</th>
                <th class="right">Price</th>
                <th class="right">Qty</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="right">{{ amountWithSymbol($item->price) }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ amountWithSymbol($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 16px;">
        <tr>
            <td style="width:60%;"></td>
            <td style="width:40%;">
                <table class="totals">
                    <tr><td class="muted">Subtotal</td><td class="right">{{ amountWithSymbol($order->subtotal) }}</td></tr>
                    @if ($order->discount > 0)
                        <tr><td class="muted">Discount{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}</td><td class="right">−{{ amountWithSymbol($order->discount) }}</td></tr>
                    @endif
                    <tr><td class="muted">Shipping</td><td class="right">{{ amountWithSymbol($order->shipping_cost) }}</td></tr>
                    <tr class="grand"><td>Total</td><td class="right">{{ amountWithSymbol($order->total) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <p class="muted" style="margin-top: 32px; text-align:center;">
        Thank you for shopping with {{ $company['name'] ?? config('app.name') }}.
    </p>
</div>
</body>
</html>
