<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class LogSuccesfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LoginEvent $event): void
    {
        // Get user IP
        $ip = Request::ip();

        // Get location details from IP
        $location = Location::get($ip);

        // Get browser details from user agent
        $agent = new Agent;
        $browser = $agent->browser();

        $os = $agent->platform();

        // Save the login log
        LoginLog::create([
            'user_id' => $event->user->id,
            'ip_address' => $ip,
            'user_agent' => Request::header('User-Agent'),
            'browser' => $browser,
            'os' => $os,
            'device' => $agent->device(),
            'country' => $location ? $location->countryName : null,
            'region' => $location ? $location->regionName : null,
            'city' => $location ? $location->cityName : null,
        ]);
    }
}
