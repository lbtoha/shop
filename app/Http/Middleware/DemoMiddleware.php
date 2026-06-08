<?php

namespace App\Http\Middleware;

use App\Exceptions\DemoException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.env') === 'demo' && in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            throw new DemoException;
        }

        return $next($request);
    }
}
