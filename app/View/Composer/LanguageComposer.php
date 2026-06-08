<?php

namespace App\View\Composer;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LanguageComposer
{
    public function compose(View $view)
    {
        $languages = Cache::rememberForever('language_list', function () {
            return \App\Models\Language::active()->select('id', 'name', 'code', 'is_default')->get();
        });
        $view->with('languages', $languages);
        $view->with('default_lang', $languages->where('is_default', true)->first() ?? $languages->first());
    }
}
