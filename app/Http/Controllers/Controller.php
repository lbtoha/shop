<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Premium Gallery BD API Documentation",
 *     version="1.0.0",
 *     description="API Documentation for Premium Gallery BD Storefront.",
 *
 *     @OA\Contact(
 *         email="support@premiumgallerybd.com"
 *     ),
 *
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */
abstract class Controller
{
    protected const TOKEN_NAME = 'pgbd-007';
}
