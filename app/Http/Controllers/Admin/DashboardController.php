<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminDashboardOverview\UserLoginLogOverview;

class DashboardController extends Controller
{
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $user_query = User::query();

        $state = [
            [
                'title' => __('Total Users'),
                'data' => (clone $user_query)->count(),
                'icon' => 'ph ph-users-three',
                'url' => route('admin.users.index'),
            ],
            [
                'title' => __('Active Users'),
                'data' => (clone $user_query)->where('status', 'active')->count(),
                'icon' => 'ph ph-user-check',
                'url' => route('admin.users.index', ['type' => 'active']),
            ],
            [
                'title' => __('Inactive Users'),
                'data' => (clone $user_query)->where('status', 'inactive')->count(),
                'icon' => 'ph ph-user-minus',
                'url' => route('admin.users.index', ['type' => 'inactive']),
            ],
            [
                'title' => __('Banned Users'),
                'data' => (clone $user_query)->where('status', 'banned')->count(),
                'icon' => 'ph ph-user-minus',
                'url' => route('admin.users.index', ['type' => 'banned']),
            ],
        ];

        return view('admin.pages.dashboard', compact('state'));
    }

    public function loginLogOverviewBYDay(UserLoginLogOverview $loginLogDaysOverview)
    {
        return response()->json($loginLogDaysOverview->overviewDaysBased());
    }

    public function loginLogOverview(UserLoginLogOverview $loginLogOSOverview)
    {
        return response()->json($loginLogOSOverview->overviewOSBased());
    }

    public function loginLogBrowserOverview(UserLoginLogOverview $loginLogBrowserOverview)
    {
        return response()->json($loginLogBrowserOverview->overviewBrowserBased());
    }
}
