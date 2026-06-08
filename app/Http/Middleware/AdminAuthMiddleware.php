<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin) {
            if (! $request->routeIs('admin.login')) {
                return redirect()->route('admin.login');
            }

            return $next($request);
        }

        if ($request->is('admin') || $request->is('admin/')) {
            return redirect()->route('admin.dashboard');
        }

        if ($this->hasUrlPermission()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }

    /**
     * Check recursively if the logged-in admin has permission
     * for the current URL.
     *
     * @param  array  $menus
     * @return bool
     */
    private function hasUrlPermission()
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin->admin_role_id) {
            return true;
        }

        $authorized_menus = $admin->role->module_caps ?? [];
        $menus = array_map(fn ($menu) => $menu['link'], getMenuCaps(config('menu.admin.menu')));

        $hasPermission = false;

        foreach ($menus as $link) {
            $url = route($link);
            if (isCurrentUrlMatched($url) && ! in_array($link, $authorized_menus)) {
                $hasPermission = true;
                break;
            }
        }

        return $hasPermission;
    }
}
