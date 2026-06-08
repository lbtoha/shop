<?php

namespace App\Http\Controllers\Admin\Settings\Language;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class LanguageJsonController extends Controller
{
    public function index(Language $language)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'lets-icons:back',
                'type' => 'link',
                'link' => route('admin.settings.languages.index'),
            ],
            [
                'label' => __('Add New Key'),
                'icon' => 'ph ph-plus-bold',
                'type' => 'modal',
                'id' => 'translate_key_add_modal',
                'href' => route('admin.settings.languages.json.store', $language->id),
            ],
        ];

        $search = request()->query('search', '');

        $translations = [];
        $collections = collect(getTranslations($language->language_file));
        if ($search) {
            $collections = $collections->filter(function ($item, $key) use ($search) {

                return stripos($key, $search) !== false;
            });
        }

        foreach ($collections->toArray() as $key => $value) {

            $translations[] = [
                'key' => $key,
                'value' => $value,
            ];
        }

        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 10);
        $offset = ($page * $perPage) - $perPage;

        $items = array_slice($translations, $offset, $perPage, true);

        $translations = new LengthAwarePaginator(
            $items, // Only grab the items we need
            count($translations), // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => request()->url(), 'query' => request()->query()] // Generate URLs
        );

        $columns = [
            [
                'label' => __('Key'),
                'key' => 'key',
                'render' => function ($language) {

                    return $language['key'];
                },
            ],
            [
                'label' => __('Value'),
                'key' => 'value',
            ],
            [
                'label' => __('Action'),
                'render' => function ($tran) use ($language) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'id' => 'translate_key_edit_modal',
                            'row' => json_encode($tran),
                            'type' => 'modal',
                            'href' => route('admin.settings.languages.json.update', $language->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'class' => 'form-submit-delete',
                            'href' => route('admin.settings.languages.json.destroy', ['language' => $language->id, 'key' => $tran['key']]),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.settings.language.translate.index', compact('language', 'translations', 'buttons', 'columns'));
    }

    public function store(Request $request, Language $language)
    {
        adminUserHasPermission(permission: 'create');

        $request->validate([
            'key' => 'required',
            'value' => 'required',
        ]);

        $json_file = $language->language_file;

        $json = getTranslations($json_file);

        $key = $request->key;

        if (isset($json[$key])) {
            return response()->json(['message' => __('Key already exists')], 400);
        }

        $json[$key] = $request->value;

        $storage = Storage::disk('lang');

        $storage->put($json_file, json_encode($json));

        return response()->json(['message' => __('Key added successfully'), 'reload' => true]);
    }

    public function update(Request $request, Language $language)
    {
        adminUserHasPermission(permission: 'edit');

        $request->validate([
            'key' => 'required',
            'value' => 'required',
        ]);

        $json_file = $language->language_file;

        $json = getTranslations($json_file);

        $key = $request->key;

        if (! isset($json[$key])) {
            return response()->json(['message' => __('Key not found')], 400);
        }

        $json[$key] = $request->value;

        $storage = Storage::disk('lang');

        $storage->put($json_file, json_encode($json));

        return response()->json(['message' => __('Key updated successfully'), 'reload' => true]);
    }

    public function destroy(Request $request, Language $language)
    {
        adminUserHasPermission(permission: 'delete');

        $request->validate([
            'key' => 'required',
        ]);

        $json_file = $language->language_file;

        $json = getTranslations($json_file);

        $key = $request->key;

        if (! isset($json[$key])) {
            return response()->json(['message' => __('Key not found')], 400);
        }

        unset($json[$key]);

        $storage = Storage::disk('lang');

        $storage->put($json_file, json_encode($json));

        return response()->json(['message' => __('Key deleted successfully'), 'reload' => true]);
    }
}
