<?php

namespace App\Http\Controllers\Admin\Extra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\Interface\Version;

class ApplicationUpdateController extends Controller
{
    private $version;

    public function __construct(Version $version)
    {
        $version->setup('quiz', null);

        $this->version = $version;
    }

    public function index()
    {
        $response = $this->version->check();

        return view('admin.pages.extra.update.index', compact('response'));
    }

    public function store(Request $request)
    {
        $fileUrl = $request->file_url;

        $isForced = boolval($request->is_forced) ?? false;

        $newTracking = boolval($request->new_tracking) ?? false;

        if (env('APP_ENV') === 'demo') {
            return response()->json([
                'message' => __('Demo version can not be updated'),
            ]);
        }

        $this->version->process($fileUrl, $isForced, $newTracking);

        return response()->json([
            'message' => __('Project updated successfully'),
            'reload' => true,
        ]);
    }
}
