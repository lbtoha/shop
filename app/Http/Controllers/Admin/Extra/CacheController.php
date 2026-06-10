<?php

namespace App\Http\Controllers\Admin\Extra;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class CacheController extends Controller
{
    public function cacheClear($command)
    {
        try {
            Artisan::call($command);
        } catch (\Exception $exception) {
            return redirect()->back()->withError($exception->getMessage());
        }

        return redirect()->back()->withSuccess(__('Cache cleared successfully'));
    }

    public function clearAll()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            // clear frontend
            $this->clearFrontendCache();
        } catch (\Exception $exception) {
            return redirect()->back()->withError($exception->getMessage());
        }

        return redirect()->back()->withSuccess(__('Cache cleared successfully'));
    }

    public function clearFrontendCache()
    {
        try {
            $response = Http::post(config('application_info.frontend_url').'/optimize');
            if ($response->failed()) {
                $response = json_decode($response->body());

                return redirect()->back()->withError($response?->message ?? 'Failed to clear frontend cache');
            }
        } catch (\Exception $exception) {
            return redirect()->back()->withError($exception->getMessage());
        }

        return redirect()->back()->withSuccess(__('Cache cleared successfully'));
    }
}
