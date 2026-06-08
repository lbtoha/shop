<x-admin-app-layout>
    @section('title', __('Pagination - ' . config('application_info.company_info.name')))
    <div class="white-box">
        <x-admin::page-header title="{{ __('Pagination Settings') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.settings.system-configurations.pagination.store') }}" method="POST"
            class="form-submit-edit">
            @csrf
            <div class="flex flex-col gap-4 xxl:gap-6">
                <x-admin::text-input-group name="per_page" :type="'number'" :value="config('extra_service.site_pagination_config.per_page')" label="Per Page" />
                <div class="input-group">
                    <x-admin::label for="sort_type">{{ __('Sort Type') }}</x-admin::label>
                    <x-admin::select-option id="sort_type" name="sort_type"
                        value="{{ config('extra_service.pagination.sort_type') }}">
                        @foreach ([['asc', 'Ascending'], ['desc', 'Descending']] as $value)
                            <option value="{{ $value[0] }}" @selected($value[0] == config('extra_service.site_pagination_config.sort_type'))>
                                {{ $value[1] }}</option>
                        @endforeach
                    </x-admin::select-option>
                    <x-admin::input-error name="sort_type" />
                </div>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
