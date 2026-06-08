<?php

namespace App\Http\Controllers\Admin\Extra;

use App\Http\Controllers\Controller;

class ApplicationInfoController extends Controller
{
    public function index()
    {
        $application_info = [
            [
                'app_name' => config('app.name'),
                'version' => config('app.version'),
                'timezone' => config('app.timezone'),

            ],
            [
                'locale' => config('app.locale'),
                'url' => config('app.url'),
                'environment' => config('app.env'),
            ],
        ];
        $server_info = [
            [
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
                'node_version' => $this->getNodeVersion(),
                'host' => gethostname() ?: 'Unknown Host',

            ], [
                'port' => $_SERVER['SERVER_PORT'] ?? 'Unknown Port',
                'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown Protocol',
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown Server Software',
                'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown Server Name',
            ],
        ];

        return view('admin.pages.extra.app-info.index', compact('application_info', 'server_info'));
    }

    private function getNodeVersion()
    {
        if (function_exists('shell_exec')) {
            $output = trim(shell_exec('node -v 2>&1'));
            if ($output) {
                return $output;
            }
        }

        if (function_exists('exec')) {
            exec('node -v 2>&1', $output, $returnVar);
            if ($returnVar === 0 && ! empty($output[0])) {
                return trim($output[0]);
            }
        }

        return 'Node.js Not Installed or cannot detect';
    }
}
