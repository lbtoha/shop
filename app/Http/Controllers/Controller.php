<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Quiz API Documentation",
 *     version="1.0.0",
 *     description="Quiz API Documentation.",
 *
 *     @OA\Contact(
 *         email="mdsafiul0073@gamil.com"
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
    protected const TOKEN_NAME = 'quiz-007';
}
