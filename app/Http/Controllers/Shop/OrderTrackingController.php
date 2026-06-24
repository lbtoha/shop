<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Display the order tracking page and perform order lookup if parameters are present.
     */
    public function show(Request $request)
    {
        $order = null;
        $searched = false;
        $error = null;

        if ($request->filled('order_number') || $request->filled('phone')) {
            $searched = true;

            $request->validate([
                'order_number' => 'required|string',
                'phone' => 'required|string',
            ]);

            $orderNumber = trim($request->order_number);
            $inputPhone = preg_replace('/[^0-9]/', '', $request->phone);

            if (empty($inputPhone)) {
                $error = __('Please provide a valid phone number.');
            } else {
                // Find order by order number
                $foundOrder = Order::where('order_number', $orderNumber)
                    ->with(['items.product', 'items.variant'])
                    ->first();

                if ($foundOrder) {
                    // Sanitize DB phone number for secure comparison
                    $dbPhone = preg_replace('/[^0-9]/', '', $foundOrder->customer_phone);
                    
                    // Compare the last 10 digits of both phone numbers to allow flexibility with prefixes (e.g. +88)
                    if (strlen($inputPhone) >= 10 && strlen($dbPhone) >= 10 && substr($dbPhone, -10) === substr($inputPhone, -10)) {
                        $order = $foundOrder;
                    } else {
                        $error = __('The phone number provided does not match our records for this order number.');
                    }
                } else {
                    $error = __('No order was found with that order number.');
                }
            }
        }

        return view('shop.track', compact('order', 'searched', 'error'));
    }
}
