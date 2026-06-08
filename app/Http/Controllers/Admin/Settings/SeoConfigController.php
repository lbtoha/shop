<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SeoConfigController extends Controller
{
    public function index(Request $request)
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-clockwise',
                'type' => 'link',
                'link' => route('admin.settings.index'),
            ],
        ];

        $seoSettings = config('seo');

        return view('admin.pages.settings.seo-configuration.index', compact('buttons', 'seoSettings'));
    }

    public function update(Request $request)
    {
        adminUserHasPermission(permission: 'edit');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'keywords' => 'required|array',
            'keywords.*' => 'required|string',
            'image' => 'required|string',
            'author' => 'required|string',
            'canonical_link' => 'required|string|url',
            'alternates' => 'required|array',
            'alternates.canonical' => 'required|string|url',

            'meta' => 'nullable|array',
            'meta.*.name' => 'nullable|string',
            'meta.*.content' => 'nullable|string',

            'openGraph' => 'nullable|array',
            'openGraph.title' => 'nullable|string',
            'openGraph.description' => 'nullable|string',
            'openGraph.type' => 'nullable|string',
            'openGraph.url' => 'nullable|string',
            'openGraph.site_name' => 'nullable|string',
            'openGraph.locale' => 'nullable|string',
            'openGraph.image' => 'nullable|string',
            'openGraph.imageAlt' => 'nullable|string',
            'openGraph.imageWidth' => 'nullable|string',
            'openGraph.imageHeight' => 'nullable|string',

            'twitter' => 'nullable|array',
            'twitter.card' => 'nullable|string',
            'twitter.site' => 'nullable|string',
            'twitter.creator' => 'nullable|string',
            'twitter.title' => 'nullable|string',
            'twitter.description' => 'nullable|string',
            'twitter.image' => 'nullable|string',

            'favicon' => 'nullable|array',
            'favicon.*.rel' => 'nullable|string',
            'favicon.*.type' => 'nullable|string',
            'favicon.*.sizes' => 'nullable|string',
            'favicon.*.href' => 'nullable|string',

            'structured_data' => 'nullable|array',
            'structured_data.script' => 'nullable|array',
            'structured_data.script.content' => 'nullable|string',
        ]);

        $validated['keywords'] = implode(',', $validated['keywords']);
        storeOption([
            'seo_meta' => $validated,
        ]);

        return back()->with('success', 'SEO Settings updated successfully');
    }

    public function sitemap(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-clockwise',
                'type' => 'link',
                'link' => route('admin.settings.index'),
            ],
            [
                'label' => __('Show Sitemap'),
                'icon' => 'ph ph-browser',
                'type' => 'link',
                'target' => '_blank',
                'link' => route('admin.settings.seo.generate.sitemap'),
            ],
        ];

        return view('admin.pages.settings.seo-configuration.sitemap', compact('buttons'));
    }

    public function sitemapUpdate(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'site_map' => 'required|string',
        ]);

        try {
            // Convert string to formatted XML
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($validated['site_map']);

            // Save the formatted XML to sitemap.xml in the public folder
            $filePath = public_path('sitemap.xml');
            File::put($filePath, $dom->saveXML());

            storeOption([
                'sitemap' => $validated['site_map'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => __('Please enter belied xml')], 400);
        }

        return back()->with('success', __('Settings updated successfully'));
    }

    public function generateSitemap()
    {
        // Retrieve sitemap data
        $sitemapData = getOption('sitemap');

        if (! $sitemapData) {
            return back()->withError(__('No sitemap data available.'));
        }

        try {
            // Convert string to formatted XML
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($sitemapData);

            // Save the formatted XML to sitemap.xml in the public folder
            $filePath = public_path('sitemap.xml');
            File::put($filePath, $dom->saveXML());

            return redirect(asset('sitemap.xml'))->with('success', __('Sitemap generated successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('Invalid XML format.'));
        }
    }

    public function robots(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-clockwise',
                'type' => 'link',
                'link' => route('admin.settings.index'),
            ],
            [
                'label' => __('Show Robots.txt'),
                'icon' => 'ph ph-browser',
                'type' => 'link',
                'target' => '_blank',
                'link' => route('admin.settings.seo.generate.robots'),
            ],
        ];

        return view('admin.pages.settings.seo-configuration.robots', compact('buttons'));
    }

    public function robotsUpdate(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'robot_text' => 'required',
        ]);

        try {
            $filePath = public_path('robots.txt');
            file_put_contents($filePath, $validated['robot_text']);
            storeOption([
                'robot_text' => $validated['robot_text'],
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => __('This not valid robot text!')], 400);
        }

        return response()->json(['message' => __('Settings updated successfully')]);
    }

    public function generateRobots()
    {
        if (! getOption('robot_text')) {
            return back()->withError(__("Didn't have robot text!"));
        }

        return redirect('/robots.txt');
    }
}
