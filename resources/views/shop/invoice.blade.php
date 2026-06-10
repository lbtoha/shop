<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
        }
        .header td {
            vertical-align: top;
        }
        .company-logo {
            font-size: 24px;
            font-weight: bold;
            color: #088178;
        }
        .company-details {
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        .invoice-details {
            margin-bottom: 30px;
            width: 100%;
        }
        .invoice-details td {
            vertical-align: top;
            width: 50%;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #111;
            margin-bottom: 10px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th {
            background-color: #f4f6f8;
            border-bottom: 2px solid #dfe3e8;
            text-align: left;
            padding: 10px;
            font-size: 12px;
            font-weight: bold;
            color: #637381;
            text-transform: uppercase;
        }
        .details-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #dfe3e8;
            vertical-align: middle;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .summary-table {
            float: right;
            width: 300px;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 6px 10px;
        }
        .summary-table tr.total td {
            border-top: 2px solid #111;
            font-weight: bold;
            font-size: 16px;
            color: #088178;
            padding-top: 10px;
        }
        .text-right {
            text-align: right;
        }
        .muted {
            color: #637381;
            font-size: 12px;
        }
        .bold {
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #dfe3e8;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #637381;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header">
            <tr>
                <td class="company-logo">
                    {{ config('application_info.company_info.name', 'Loom By Megh') }}
                </td>
                <td class="company-details">
                    <strong>{{ config('application_info.company_info.name', 'Loom By Megh') }}</strong><br>
                    {{ config('application_info.company_info.address', 'Dhaka, Bangladesh') }}<br>
                    Phone: {{ config('application_info.company_info.phone', '+880 123456789') }}<br>
                    Email: {{ config('application_info.company_info.email', 'info@loombymegh.com') }}
                </td>
            </tr>
        </table>

        <hr style="border: 0; border-top: 1px solid #dfe3e8; margin-bottom: 30px;">

        <table class="invoice-details">
            <tr>
                <td>
                    <div class="title">Bill To:</div>
                    <strong>{{ $order->customer_name }}</strong><br>
                    Phone: {{ $order->customer_phone }}<br>
                    @if($order->customer_email)
                        Email: {{ $order->customer_email }}<br>
                    @endif
                    Address: {{ $order->shipping_address }}
                </td>
                <td class="text-right">
                    <div class="title" style="color: #088178;">INVOICE</div>
                    <strong>Invoice #:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('Y-m-d') }}<br>
                    <strong>Payment Method:</strong> Cash on Delivery
                </td>
            </tr>
        </table>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-right" style="width: 100px;">Price</th>
                    <th class="text-right" style="width: 80px;">Qty</th>
                    <th class="text-right" style="width: 120px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            <div class="bold">{{ $item->product_name }}</div>
                        </td>
                        <td class="text-right">{{ amountWithSymbol($item->price) }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ amountWithSymbol($item->subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td class="muted">Subtotal:</td>
                <td class="text-right">{{ amountWithSymbol($order->subtotal) }}</td>
            </tr>
            <tr>
                <td class="muted">Shipping:</td>
                <td class="text-right">{{ $order->shipping_cost > 0 ? amountWithSymbol($order->shipping_cost) : 'Free' }}</td>
            </tr>
            <tr class="total">
                <td>Total:</td>
                <td class="text-right">{{ amountWithSymbol($order->total) }}</td>
            </tr>
        </table>

        <div style="clear: both;"></div>

        <div class="footer">
            <p>Thank you for shopping with us!</p>
            <p class="muted">This is a computer-generated invoice and does not require a signature.</p>
        </div>
    </div>
</body>
</html>
