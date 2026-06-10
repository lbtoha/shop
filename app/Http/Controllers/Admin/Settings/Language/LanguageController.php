<?php

namespace App\Http\Controllers\Admin\Settings\Language;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LanguageRequest;
use App\Models\Language;
use App\Models\Option;
use App\Services\Helper\JsonHelper;
use App\Services\ModalIndexQuey;
use App\Traits\MediaUploader;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LanguageController extends Controller
{
    use MediaUploader;

    public function __construct(
        public JsonHelper $json_helper
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $language_add_modal_id = 'language_add_modal_id';
        $language_edit_modal_id = 'language_edit_modal_id';
        $buttons = [
            [
                'label' => __('Add New Language'),
                'icon' => 'ph ph-plus-bold',
                'type' => 'modal',
                'id' => $language_add_modal_id,
                'href' => route('admin.settings.languages.store'),
            ],
        ];

        $languages = ModalIndexQuey::get(model: Language::query());

        $columns = [
            [
                'label' => __('Name'),
                'key' => 'name',
                'header_class' => 'lg:w-[350px]',
                'is_sortable' => true,
            ],
            [
                'label' => __('Code'),
                'key' => 'code',
                'header_class' => 'lg:w-[200px]',
                'is_sortable' => true,
            ],

            [
                'label' => __('Flag'),
                'key' => 'flag_code',
            ],
            [
                'label' => __('Status'),
                'key' => 'status',
                'is_sortable' => true,
                'render' => function ($language) {
                    $color = $language->status == 'active' ? 'success' : 'danger';

                    return '<span class="status '.$color.' capitalize">'.__($language->status).'</span>';
                },
            ],
            [
                'label' => __('Is Default'),
                'key' => 'is_default',
                'is_sortable' => true,
                'render' => function ($language) {
                    $color = $language->is_default ? 'success' : 'danger';
                    $text = $language->is_default ? __('Yes') : __('No');

                    return '<span class="status '.$color.' capitalize">'.$text.'</span>';
                },
            ],
            [
                'label' => __('Action'),
                'render' => function ($language) use ($language_edit_modal_id) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'id' => $language_edit_modal_id,
                            'row' => $language,
                            'type' => 'modal',
                            'href' => route('admin.settings.languages.update', $language->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.settings.languages.destroy', $language->id),
                        ],
                        [
                            'label' => $language->status == 'active' ? __('InActive') : __('Active'),
                            'icon' => $language->status == 'active' ? 'ph ph-toggle-left' : 'ph ph-toggle-right',
                            'type' => 'link',
                            'href' => route('admin.settings.languages.show', $language),
                        ],
                        [
                            'label' => $language->is_default ? __('Default') : __('Set as Default'),
                            'icon' => 'ph ph-star',
                            'type' => 'link',
                            'href' => route('admin.settings.languages.edit', $language),
                        ],
                        [
                            'label' => __('Edit Translations'),
                            'icon' => 'ph ph-translate',
                            'type' => 'link',
                            'href' => route('admin.settings.languages.json.edit', $language->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.settings.language.index', compact(
            'buttons',
            'languages',
            'columns',
            'language_add_modal_id',
            'language_edit_modal_id',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LanguageRequest $request)
    {
        adminUserHasPermission(permission: 'create');
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($validated['is_default']) {
                Language::query()->update(['is_default' => false]);
            }

            if ($request->hasFile('language_file') && $this->json_helper->checkValidJson($request->file('language_file'))) {
                $validated['language_file'] = $this->json_helper->uploadLanguageFile($validated);
            } else {
                $validated['language_file'] = $this->json_helper->copyLanguageFileFromHelper(language_code: $validated['code']);
            }

            Language::create($validated);

            Cache::forget('lang_list');
            Cache::forget('language_list');

            if ($validated['is_default']) {
                Option::updateOption(
                    'default_language',
                    $validated['code'],
                );
                session()->put('locale', $validated['code']);
            }

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json(['message' => __('Language created successfully'), 'reload' => true]);
    }

    public function edit(Language $language)
    {
        adminUserHasPermission(permission: 'edit');

        if ($language->status != 'active') {
            return redirect()->back()->withError(__('Only active language can be set as default'));
        }

        try {
            DB::beginTransaction();

            Language::query()->update(['is_default' => false]);

            $language->update(['is_default' => true]);

            Cache::forget('lang_list');
            Cache::forget('language_list');

            Option::updateOption(
                'default_language',
                $language->code,
            );

            session()->put('locale', $language->code);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withError($th->getMessage());
        }

        return redirect()->back()->withSuccess(__('Language set as default successfully'));
    }

    public function show(Language $language)
    {
        adminUserHasPermission(permission: 'edit');
        $language->update(['status' => $language->status == __('active') ? __('inactive') : __('active')]);

        return redirect()->back()->withSuccess(__('Language status change successfully'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageRequest $request, Language $language)
    {
        adminUserHasPermission(permission: 'edit');
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($language->is_default && ! $validated['is_default']) {
                throw new \Exception('Default language cannot be edited');
            }

            if ($validated['is_default'] == '1') {
                Language::query()->where('id', '!=', $language->id)->update(['is_default' => false]);
            }

            if ($request->hasFile('language_file') && $this->json_helper->checkValidJson($request->file('language_file'))) {

                if (! $this->json_helper->deleteJsonFile($language->language_file)) {
                    throw new \Exception(__('Failed to delete language file'));
                }

                $validated['language_file'] = $this->json_helper->uploadLanguageFile($validated, 'language_file');
            }

            $language->update([
                ...$validated,
            ]);

            if ($language->is_default) {
                Option::updateOption(
                    'default_language',
                    $language->code,
                );

                session()->put('locale', $language->code);
            }

            Cache::forget('lang_list');
            Cache::forget('language_list');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json(['message' => __('Language updated successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        adminUserHasPermission(permission: 'delete');

        if (! $this->json_helper->deleteJsonFile($language->language_file)) {
            return redirect()->back()->withError(__('Failed to delete language file'));
        }

        DB::transaction(function () use ($language) {
            if ($language->is_default) {
                Option::updateOption(
                    'default_language',
                    'en',
                );
                session()->put('locale', 'en');
            }

            $language->delete();
        });

        Cache::forget('lang_list');
        Cache::forget('language_list');

        return response()->json(['message' => __('Language deleted successfully')]);
    }

    public function changeLang($local)
    {
        adminUserHasPermission(permission: 'edit');

        App::setLocale($local);

        session()->put('locale', $local);

        Cache::forget('lang_list');
        Cache::forget('language_list');

        return redirect()->back()->withSuccess(__('Language changed successfully'));
    }

    public function downloadHelpJson()
    {
        adminUserHasPermission(permission: 'edit');

        return Storage::disk('lang')->download('lang/help.json');
    }
}
