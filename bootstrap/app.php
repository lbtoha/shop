<?php

use App\Http\Middleware\SetAppLocal;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        using: function () {
            /**
             * storefront (public shop) routes — home, shop, product, cart, checkout
             */
            Route::middleware(['web', SetAppLocal::class])
                ->group(base_path('routes/shop.php'));
            /**
             * admin route
             */
            Route::middleware(['web', SetAppLocal::class])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            /**
             * not found route
             */
            Route::middleware(['web', SetAppLocal::class])
                ->name('not.found')
                ->get('/not-found', function () {
                    return view('errors.404');
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (($request->wantsJson() || $request->is('api/*'))) {

                $messages = collect($exception->errors())->mapWithKeys(function ($messages, $field) {
                    return [$field => $messages[0]];
                })->toArray();

                return response()->json([
                    'statusCode' => 422,
                    'message' => $exception->getMessage(),
                    'errors' => $messages,
                ], 422);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (($request->is('api/*') || $request->wantsJson()) && $e->getStatusCode() === 404) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Record not found.',
                ], 404);
            }

            if (! $request->wantsJson() && $e->getStatusCode() === 404) {
                return redirect()->intended(route('not.found'));
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {

            if ($request->wantsJson() && $request->is('api/*') && $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                           && $e->getStatusCode() === 503) {
                $maintenanceModeData = getOptionWithJsonDecode('maintenance_mode');

                if (! is_array($maintenanceModeData)) {
                    $maintenanceModeData = [];
                }

                // Remove secret_key safely
                if (isset($maintenanceModeData['login_secret_key'])) {
                    unset($maintenanceModeData['login_secret_key']);
                }

                return response()->json([
                    'statusCode' => 503,
                    'data' => $maintenanceModeData,
                ], 503);

            }
        });
    })
    ->create();
