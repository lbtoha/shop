@php
    $currency = getOption('currency_code', 'BDT');
    $fbPixelEnabled = (bool) config('extension.facebook_pixel.is_enabled') && !empty(config('extension.facebook_pixel.pixel_id'));
    $ga4Enabled = (bool) config('extension.google_analytics.is_enabled') && !empty(config('extension.google_analytics.measurement_id'));
    $tiktokPixelEnabled = (bool) config('extension.tiktok_pixel.is_enabled') && !empty(config('extension.tiktok_pixel.pixel_id'));
    $clarityEnabled = (bool) config('extension.clarity.is_enabled') && !empty(config('extension.clarity.project_id'));
    $googleAdsEnabled = (bool) config('extension.google_ads.is_enabled') && !empty(config('extension.google_ads.conversion_id'));
@endphp

@if ($fbPixelEnabled)
    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        
        @php
            $fbUserData = [];
            if (request()->routeIs('shop.checkout.confirmation') && isset($order)) {
                if ($order->customer_email) {
                    $fbUserData['em'] = strtolower(trim($order->customer_email));
                }
                if ($order->customer_phone) {
                    $phoneClean = preg_replace('/[^0-9]/', '', $order->customer_phone);
                    if (strlen($phoneClean) === 11 && str_starts_with($phoneClean, '01')) {
                        $phoneClean = '88' . $phoneClean;
                    }
                    $fbUserData['ph'] = $phoneClean;
                }
                if ($order->customer_name) {
                    $parts = explode(' ', trim($order->customer_name));
                    $fbUserData['fn'] = strtolower(trim($parts[0]));
                    if (isset($parts[1])) {
                        $fbUserData['ln'] = strtolower(trim(end($parts)));
                    }
                }
            } elseif (auth()->check()) {
                $authUser = auth()->user();
                if ($authUser->email) {
                    $fbUserData['em'] = strtolower(trim($authUser->email));
                }
                if (!empty($authUser->phone)) {
                    $phoneClean = preg_replace('/[^0-9]/', '', $authUser->phone);
                    if (strlen($phoneClean) === 11 && str_starts_with($phoneClean, '01')) {
                        $phoneClean = '88' . $phoneClean;
                    }
                    $fbUserData['ph'] = $phoneClean;
                }
                if (!empty($authUser->name)) {
                    $parts = explode(' ', trim($authUser->name));
                    $fbUserData['fn'] = strtolower(trim($parts[0]));
                    if (isset($parts[1])) {
                        $fbUserData['ln'] = strtolower(trim(end($parts)));
                    }
                }
            }
        @endphp

        @if (!empty($fbUserData))
            fbq('init', '{{ config("extension.facebook_pixel.pixel_id") }}', {!! json_encode($fbUserData) !!});
        @else
            fbq('init', '{{ config("extension.facebook_pixel.pixel_id") }}');
        @endif
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
             src="https://www.facebook.com/tr?id={{ config('extension.facebook_pixel.pixel_id') }}&ev=PageView&noscript=1"
        />
    </noscript>
    <!-- End Meta Pixel Code -->
@endif

@if ($ga4Enabled || $googleAdsEnabled)
    <!-- Google Tag (gtag.js) -->
    @php
        $primaryGoogleId = $ga4Enabled ? config('extension.google_analytics.measurement_id') : config('extension.google_ads.conversion_id');
    @endphp
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $primaryGoogleId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        @if ($ga4Enabled)
            gtag('config', '{{ config("extension.google_analytics.measurement_id") }}');
        @endif
        @if ($googleAdsEnabled)
            gtag('config', '{{ config("extension.google_ads.conversion_id") }}');
        @endif
    </script>
    <!-- End Google Tag -->
@endif

@if ($tiktokPixelEnabled)
    <!-- TikTok Pixel Code -->
    <script>
        !function (w, d, t) {
            w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var e=0;e<ttq.methods.length;e++)ttq.setAndDefer(ttq,ttq.methods[e]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.mixpool;w[t].localSdkUrl=r,w[t]._i=w[t]._i||{},w[t]._i[e]=[],w[t]._i[e]._u=r,w[t]._t=w[t]._t||{},w[t]._t[e]=+new Date,w[t]._o=w[t]._o||{},w[t]._o[e]=n||{};var a=d.createElement("script");a.type="text/javascript",a.async=!0,a.src=r+"?sdkid="+e+"&lib="+t;var i=d.getElementsByTagName("script")[0];i.parentNode.insertBefore(a,i)};
            ttq.load('{{ config("extension.tiktok_pixel.pixel_id") }}');
            ttq.page();
        }(window, document, 'ttq');
    </script>
    <!-- End TikTok Pixel Code -->
@endif

@if ($clarityEnabled)
    <!-- Microsoft Clarity Code -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window,document,"clarity","script","{{ config('extension.clarity.project_id') }}");
    </script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- 1. Product Detail Page (ViewContent / view_item) ---
        @if (request()->routeIs('shop.product') && isset($product))
            var productData = {
                id: '{{ $product->id }}',
                name: '{{ addslashes($product->name) }}',
                category: '{{ $product->category ? addslashes($product->category->name) : "" }}',
                price: {{ (float) $product->price }},
                currency: '{{ $currency }}'
            };

            // Facebook
            if (window.fbq) {
                fbq('track', 'ViewContent', {
                    content_ids: [productData.id],
                    content_type: 'product',
                    content_name: productData.name,
                    content_category: productData.category,
                    value: productData.price,
                    currency: productData.currency
                });
            }

            // Google
            if (window.gtag) {
                gtag('event', 'view_item', {
                    currency: productData.currency,
                    value: productData.price,
                    items: [{
                        item_id: productData.id,
                        item_name: productData.name,
                        item_category: productData.category,
                        price: productData.price,
                        quantity: 1
                    }]
                });
            }

            // TikTok
            if (window.ttq) {
                ttq.track('ViewContent', {
                    contents: [{
                        content_id: productData.id,
                        content_name: productData.name,
                        content_category: productData.category,
                        price: productData.price,
                        quantity: 1
                    }],
                    value: productData.price,
                    currency: productData.currency
                });
            }
        @endif

        // --- 2. Checkout Page (InitiateCheckout / begin_checkout) ---
        @if (request()->routeIs('shop.checkout.index') && isset($items) && count($items) > 0)
            var checkoutItems = [
                @foreach ($items as $line)
                    {
                        item_id: '{{ $line["product"]->id }}',
                        item_name: '{{ addslashes($line["product"]->name) }}',
                        item_category: '{{ $line["product"]->category ? addslashes($line["product"]->category->name) : "" }}',
                        price: {{ (float) $line["unit_price"] }},
                        quantity: {{ (int) $line["quantity"] }}
                    },
                @endforeach
            ];
            var checkoutTotal = {{ (float) $subtotal }};
            var checkoutCurrency = '{{ $currency }}';

            // Facebook
            if (window.fbq) {
                fbq('track', 'InitiateCheckout', {
                    content_ids: checkoutItems.map(function(item) { return item.item_id; }),
                    content_type: 'product',
                    value: checkoutTotal,
                    currency: checkoutCurrency,
                    num_items: checkoutItems.reduce(function(acc, item) { return acc + item.quantity; }, 0)
                });
            }

            // Google
            if (window.gtag) {
                gtag('event', 'begin_checkout', {
                    currency: checkoutCurrency,
                    value: checkoutTotal,
                    items: checkoutItems
                });
            }

            // TikTok
            if (window.ttq) {
                ttq.track('InitiateCheckout', {
                    contents: checkoutItems.map(function(item) {
                        return {
                            content_id: item.item_id,
                            content_name: item.item_name,
                            content_category: item.item_category,
                            price: item.price,
                            quantity: item.quantity
                        };
                    }),
                    value: checkoutTotal,
                    currency: checkoutCurrency
                });
            }
        @endif

        // --- 3. Order Success/Confirmation Page (Purchase / CompletePayment) ---
        @if (request()->routeIs('shop.checkout.confirmation') && isset($order))
            var purchaseItems = [
                @foreach ($order->items as $item)
                    {
                        item_id: '{{ $item->product_id }}',
                        item_name: '{{ addslashes($item->product_name) }}',
                        price: {{ (float) $item->price }},
                        quantity: {{ (int) $item->quantity }}
                    },
                @endforeach
            ];
            var purchaseTotal = {{ (float) $order->total }};
            var purchaseCurrency = '{{ $currency }}';
            var orderId = '{{ $order->order_number }}';

            // Facebook
            if (window.fbq) {
                fbq('track', 'Purchase', {
                    content_ids: purchaseItems.map(function(item) { return item.item_id; }),
                    content_type: 'product',
                    value: purchaseTotal,
                    currency: purchaseCurrency,
                    num_items: purchaseItems.reduce(function(acc, item) { return acc + item.quantity; }, 0)
                }, {
                    eventID: orderId
                });
            }

            // Google
            if (window.gtag) {
                gtag('event', 'purchase', {
                    transaction_id: orderId,
                    currency: purchaseCurrency,
                    value: purchaseTotal,
                    items: purchaseItems
                });
            }

            // Google Ads Conversion
            @if ($googleAdsEnabled && !empty(config('extension.google_ads.purchase_label')))
                if (window.gtag) {
                    gtag('event', 'conversion', {
                        'send_to': '{{ config("extension.google_ads.conversion_id") }}/{{ config("extension.google_ads.purchase_label") }}',
                        'value': purchaseTotal,
                        'currency': purchaseCurrency,
                        'transaction_id': orderId
                    });
                }
            @endif

            // TikTok
            if (window.ttq) {
                ttq.track('CompletePayment', {
                    contents: purchaseItems.map(function(item) {
                        return {
                            content_id: item.item_id,
                            content_name: item.item_name,
                            price: item.price,
                            quantity: item.quantity
                        };
                    }),
                    value: purchaseTotal,
                    currency: purchaseCurrency
                }, {
                    event_id: orderId
                });
            }
        @endif

        // --- 4. AJAX AddToCart Event Handler ---
        document.addEventListener("cart:added", function (e) {
            var detail = e.detail;
            if (!detail) return;

            var addTotal = parseFloat(detail.price) * parseInt(detail.quantity);
            var addCurrency = '{{ $currency }}';

            // Facebook
            if (window.fbq) {
                fbq('track', 'AddToCart', {
                    content_ids: [detail.productId],
                    content_type: 'product',
                    content_name: detail.name,
                    value: addTotal,
                    currency: addCurrency
                });
            }

            // Google
            if (window.gtag) {
                gtag('event', 'add_to_cart', {
                    currency: addCurrency,
                    value: addTotal,
                    items: [{
                        item_id: detail.productId,
                        item_name: detail.name,
                        price: parseFloat(detail.price),
                        quantity: parseInt(detail.quantity)
                    }]
                });
            }

            // TikTok
            if (window.ttq) {
                ttq.track('AddToCart', {
                    contents: [{
                        content_id: detail.productId,
                        content_name: detail.name,
                        price: parseFloat(detail.price),
                        quantity: parseInt(detail.quantity)
                    }],
                    value: addTotal,
                    currency: addCurrency
                });
            }
        });
    });
</script>
