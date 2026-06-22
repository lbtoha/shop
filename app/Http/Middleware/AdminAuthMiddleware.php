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

        if ($this->isDeniedForCurrentUrl()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }

    /**
     * Whether the logged-in admin is denied access to the current URL.
     *
     * An admin is denied when the current URL maps to a known admin menu that is
     * NOT in their role's authorized menus. Admins with no role (admin_role_id
     * null) are superusers and are never denied.
     */
    private function isDeniedForCurrentUrl(): bool
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin->admin_role_id) {
            return false; // Superuser — full access.
        }

        $authorized_menus = $admin->role->module_caps ?? [];
        $menus = array_map(fn ($menu) => $menu['link'], getMenuCaps(config('menu.admin.menu')));

        foreach ($menus as $link) {
            $url = route($link);
            if (isCurrentUrlMatched($url) && ! in_array($link, $authorized_menus)) {
                return true; // Current URL is a menu this admin isn't authorized for.
            }
        }

        return false;
    }
}
