<?php

namespace App\Services\AdminDashboardOverview;

use App\Models\LoginLog;
use Carbon\Carbon;

class UserLoginLogOverview extends BaseOverview
{
    /**
     * Return an array of the login counts grouped by OS for the current year.
     *
     * The returned array will have the following structure:
     * [
     *     'series' => [int, int, ...], // The count of logins for each OS
     *     'labels' => [string, string, ...], // The OS names
     * ]
     */
    public function overviewOSBased()
    {
        // Fetch login counts grouped by normalized OS
        $logs = LoginLog::query()
            ->selectRaw("CASE WHEN os IS NULL OR os = '0' OR TRIM(os) = '' THEN 'Other' ELSE os END AS os_group, COUNT(id) as total")
            ->whereYear('created_at', now()->year)
            ->groupBy('os_group')
            ->orderByDesc('total')
            ->get();

        return [
            'series' => $logs->pluck('total')->toArray(),
            'labels' => $logs->pluck('os_group')->toArray(),
        ];
    }

    public function overviewBrowserBased()
    {
        $logs = LoginLog::query()
            ->selectRaw("CASE WHEN browser IS NULL OR browser = '0' OR TRIM(browser) = '' THEN 'Other' ELSE browser END AS browser_group, COUNT(id) as total")
            ->groupBy('browser_group')
            ->orderByDesc('total')
            ->get();

        return [
            'series' => $logs->pluck('total')->toArray(),
            'labels' => $logs->pluck('browser_group')->toArray(),
        ];
    }

    public function overviewDaysBased()
    {
        $from_date = Carbon::now()->subDays(15)->toDateString();
        $to_date = now()->toDateString();

        $logs = LoginLog::query()
            ->selectRaw('DATE(created_at) as date, os, COUNT(id) as total')
            ->whereDate('created_at', '>=', $from_date)
            ->groupBy('date', 'os')
            ->orderBy('date')
            ->get();

        $days = $this->getDays($from_date, $to_date);

        $osList = $logs->pluck('os')->unique();
        $series = [];

        foreach ($osList as $os) {
            $data = [];
            foreach ($days as $day) {
                $count = $logs->firstWhere(fn ($log) => $log->date === $day && $log->os === $os)?->total ?? 0;
                $data[] = $count;
            }
            $series[] = [
                'name' => $os,
                'data' => $data,
            ];
        }

        return [
            'series' => $series,
            'labels' => $days,
        ];
    }
}
