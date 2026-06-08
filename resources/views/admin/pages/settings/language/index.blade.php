<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Languages') }}" :buttons="$buttons" />
        <x-admin::table :columns="$columns" :data="$languages" />
        <x-admin::modal modalId="{{ $language_add_modal_id }}" title="Add New Language">
            <form class="form-submit-add" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="name" label="Name" placeholder="Enter Your Name" />
                    <x-admin::text-input-group name="code" label="Code" placeholder="Enter Your Name" />
                    <x-admin::text-input-group name="flag_code" label="Flag Code" placeholder="Enter Your Name" />

                    <div class="relative">
                        <div class="flex justify-between items-center">
                            <x-admin::label for="language_file">{{ __('Upload Json File') }}</x-admin::label>
                            <button type="button" data-modal-target="json_help">
                                <i class="ph ph-question text-2xl"></i></button>
                        </div>
                        <x-admin::file-input id="language_file" type="file" name="language_file"
                            label="Upload Json File" required autofocus />
                        <x-admin::input-error name="language_file" />
                    </div>

                    <div>
                        <x-admin::label for="status">{{ __('Select Status') }}</x-admin::label>
                        <x-admin::switch label="Status" name="status" />
                        <x-admin::input-error name="status" />
                    </div>
                    <div>
                        <x-admin::label for="is_default">{{ __('Select Default') }}</x-admin::label>
                        <x-admin::switch label="Make Default" value="1" name="is_default" :types="[['label' => __('No'), 'value' => 0], ['label' => __('Yes'), 'value' => 1]]" />
                        <x-admin::input-error name="is_default" />
                    </div>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button type="submit">
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
        <x-admin::modal modalId="{{ $language_edit_modal_id }}" title="Edit Language">
            <form class="form-submit-edit" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="name" label="Name" placeholder="Enter Your Name" />
                    <x-admin::text-input-group name="code" label="Code" placeholder="Enter Your Name" />
                    <x-admin::text-input-group name="flag_code" label="Flag Code" placeholder="Enter Your Name" />

                    <div class="relative">
                        <div class="flex justify-between items-center">
                            <x-admin::label for="language_file">{{ __('Upload Json File') }}</x-admin::label>
                            <button type="button" data-modal-target="json_help">
                                <i class="ph ph-question text-2xl"></i></button>
                        </div>
                        <x-admin::file-input id="language_file" type="file" name="language_file"
                            label="Upload Json File" required autofocus />
                        <x-admin::input-error name="language_file" />
                    </div>

                    <div>
                        <x-admin::label for="status">{{ __('Select Status') }}</x-admin::label>
                        <x-admin::switch label="Status" name="status" />
                        <x-admin::input-error name="status" />
                    </div>
                    <div>
                        <x-admin::label for="is_default">{{ __('Select Default') }}</x-admin::label>
                        <x-admin::switch label="Make Default" value="1" name="is_default" :types="[['label' => __('No'), 'value' => 0], ['label' => __('Yes'), 'value' => 1]]" />
                        <x-admin::input-error name="is_default" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <x-admin::primary-button>
                            {{ __('Save') }}
                        </x-admin::primary-button>
                    </div>
            </form>
        </x-admin::modal>

    </div>
    <x-admin::modal title="JSON Help" modalId="json_help">
        <div class="p-4">
            <p className="mb-4">
                {{ __('Upload a JSON file containing your language translations to set up your translations. You need to download a JSON file and upload it here.') }}
            </p>

            <div class="my-2 flex justify-center">
                <x-admin::primary-button
                    href="{{ route('admin.settings.languages.download-help-json') }}">{{ __('Download JSON file') }}</x-admin::primary-button>
            </div>

            <h3 className="text-xl font-semibold mb-2">{{ __('Instructions') }}:</h3>
            <ol className="list-decimal list-inside space-y-2">
                <li>{{ __('Download The JSON file from the link above.') }}</li>
                <li>{{ __('Translate the keys in the JSON file.') }}</li>
                <li>{{ __('Save the translated JSON file.') }}</li>
                <li>{{ __('Ensure all languages have the same set of keys.') }}</li>
                <li>{{ __('Save the file with a .json extension.') }}</li>
                <li>{{ __('Upload the file using the file input above.') }}</li>
            </ol>
            <p className="mt-4 text-sm text-gray-600">
                {{ __('Note: The JSON structure should be valid. You can use online JSON validators to check your file before uploading.') }}
            </p>
        </div>
    </x-admin::modal>
</x-admin-app-layout>
