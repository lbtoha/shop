<?php

namespace App\Handlers;

class LfmConfigHandler extends \UniSharp\LaravelFilemanager\Handlers\ConfigHandler
{
    public function userField()
    {
        $admin = auth()->guard('admin')->user();

        if ($admin) {
            return "admin$admin->id";
        }

        return 'public'; // Fallback for unauthenticated users
    }
}
