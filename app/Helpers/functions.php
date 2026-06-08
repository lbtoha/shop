<?php

use App\Exceptions\CustomWebException;
use App\Models\Option;
use App\Services\Helper\JsonCleaner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Stevebauman\Purify\Facades\Purify;

if (! function_exists('convertCurrency')) {
    function convertCurrency($amount, $to = null, $from = null)
    {
        // Multi-currency removed: amount is returned unchanged.
        return is_numeric($amount) ? $amount : ($amount ?? 0);
    }
}

if (! function_exists('currencyRate')) {
    function currencyRate($to = null, $from = null)
    {
        // Multi-currency removed: rate is always 1.
        return 1;
    }
}

if (! function_exists('defaultCurrency')) {
    function defaultCurrency()
    {
        $symbol = getOption('currency_symbol', '$');

        return (object) [
            'code' => getOption('currency_code', 'USD'),
            'symbol' => $symbol,
            'symbol_position' => 'left',
            'rate' => 1,
        ];
    }
}

if (! function_exists('getTranslations')) {
    function getTranslations($file)
    {
        $storage = Storage::disk('lang');
        if (! $file) {
            return [];
        }

        if (! $storage->exists($file)) {
            return [];
        }

        $json_cleaner = new JsonCleaner;

        return $json_cleaner->cleanAndValidateJson(file_get_contents(
            $storage->path($file)));
    }
}

if (! function_exists('storeOption')) {
    /**
     * Stores options in the database.
     *
     * @param  array  $options  An array of key-value pairs to be stored.
     * @return void
     *
     * @throws \Exception If the options are not an array.
     * @throws \Throwable Any other exception that occurs during the storage process.
     */
    function storeOption(array $options)
    {
        if (! is_array($options)) {
            throw new \Exception('Options should be an array');
        }

        try {
            DB::beginTransaction();
            foreach ($options as $key => $value) {
                Option::updateOption($key, $value);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}

if (! function_exists('getOptionWithJsonDecode')) {
    function getOptionWithJsonDecode($key, $default = null)
    {
        $option = Option::getOption($key);
        if (! $option) {
            return $default;
        }

        return json_decode($option, true);
    }
}

if (! function_exists('getOption')) {
    function getOption($key, $default = null)
    {
        return Option::getOption($key, $default);
    }
}

if (! function_exists('currencySymbol')) {
    function currencySymbol($code = null)
    {
        return getOption('currency_symbol', '$');
    }
}
if (! function_exists('amountWithSymbol')) {
    function amountWithSymbol(float|string $amount, $code = null)
    {
        return getOption('currency_symbol', '$').$amount;
    }
}

if (! function_exists('adminUserHasPermission')) {
    function adminUserHasPermission(string $permission)
    {
        /**
         * @var \App\Models\Admin $admin
         */
        $admin = auth('admin')->user();

        if (! $admin) {
            throw new CustomWebException('Permission denied');
        }

        if (! $admin->hasCap($permission)) {
            throw new CustomWebException('Permission denied');
        }

        return true;
    }
}

if (! function_exists('inputSanitize')) {
    function inputSanitize($input)
    {
        return Purify::clean($input);
    }
}

if (! function_exists('optimizeImage')) {
    function optimizeImage($image)
    {
        $fileType = File::mimeType($image);
        if ($fileType === 'image/jpeg' || $fileType === 'image/png' || $fileType === 'image/jpg') {
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($image);
        }
    }
}

if (! function_exists('placeAvatar')) {
    function placeAvatar($image, $text)
    {
        return $image ? $image : 'https://ui-avatars.com/api/?name='.$text;
    }
}

if (! function_exists('placeImage')) {
    function placeImage($image)
    {
        return ! is_null($image) || $image ? $image : '/assets/client/media/images/placeholder.png';
    }
}

if (! function_exists('getJsonFile')) {
    function getJsonFile($file, $disk = 'lang')
    {
        $json_cleaner = new JsonCleaner;
        // this is for only public path files
        if ($disk == 'public_path') {
            return $json_cleaner->cleanAndValidateJson(file_get_contents(public_path($file)));
        }

        $storage = Storage::disk($disk);
        if (! $file) {
            return [];
        }

        if (! $storage->exists($file)) {
            return [];
        }

        return $json_cleaner->cleanAndValidateJson(file_get_contents(
            $storage->path($file)));
    }
}

if (! function_exists('getNotifications')) {
    function getNotifications()
    {
        $query = \App\Models\Notification::query()->whereNull('read_at');

        $admin = auth('admin')->user();

        if ($admin) {
            $query->where('notifiable_type', 'App\Models\Admin');
        }

        $list = $query->orderBy('created_at', 'desc')
            ->limit(value: 5)
            ->get();

        $count = (clone $query)->count();

        return [
            'list' => $list,
            'count' => $count,
        ];
    }
}

if (! function_exists('getValueFromArray')) {
    function getValueFromArray(mixed $object, string $path, mixed $default = null): mixed
    {
        return translateText(data_get($object, $path, $default));
    }
}

if (! function_exists('authorizedMenus')) {
    function authorizedMenus(array $menus, $user, $child_key = 'submenus')
    {
        $temp = [];

        $menu_caps = $user?->role?->module_caps ?? [];
        foreach ($menus as $menu) {
            if (isset($menu['submenus'])) {
                $subMenus = authorizedMenus($menu['submenus'], $user, $child_key);
                if (! empty($subMenus)) {
                    $temp[] = [
                        ...$menu,
                        'submenus' => authorizedMenus($menu['submenus'], $user, $child_key),
                    ];
                }
            } else {
                if (in_array($menu['link'], $menu_caps)) {
                    $temp[] = $menu;
                }
            }
        }

        return $temp;
    }
}

if (! function_exists('translateText')) {
    function translateText(mixed $value)
    {
        return is_string($value) ? __($value) : $value;
    }
}

if (! function_exists('getMenuCaps')) {
    function getMenuCaps($mens)
    {
        $temp = [];

        foreach ($mens as $menu) {
            if (isset($menu['submenus'])) {
                $temp = [...$temp, ...getMenuCaps($menu['submenus'])];
            } else {
                $temp[] = [
                    'title' => $menu['title'],
                    'link' => $menu['link'],
                ];
            }
        }

        return $temp;
    }
}

if (! function_exists('getModelById')) {
    function getModelById($model, $id)
    {
        if (is_array($id)) {
            return $model::whereIn('id', $id)->get();
        } else {
            return $model::where('id', $id)->first();
        }
    }
}

if (! function_exists('snackCaseToNormalWord')) {
    function snackCaseToNormalWord($str)
    {
        if (! $str) {
            return '';
        }

        return ucwords(str_replace('_', ' ', $str));
    }
}

if (! function_exists('isSameUrlForQueryParams')) {
    function isSameUrlForQueryParams(string $url1, string $url2, array $params = []): bool
    {
        if (Str::before($url1, '?') !== Str::before($url2, '?')) {
            return false;
        }
        $query1 = [];
        $query2 = [];
        parse_str(parse_url($url1, PHP_URL_QUERY) ?? '', $query1);
        parse_str(parse_url($url2, PHP_URL_QUERY) ?? '', $query2);
        foreach ($params as $param) {
            if (($query1[$param] ?? null) !== ($query2[$param] ?? null)) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('isCurrentUrlMatched')) {
    function isCurrentUrlMatched(string $url): bool
    {
        $currentUrl = request()->fullUrl();
        // If it's a route name, convert to full URL
        if (Str::contains($url, '.') && ! Str::startsWith($url, ['http://', 'https://'])) {
            $url = route($url);
        }
        // Exact match
        if ($currentUrl === $url) {
            return true;
        }

        if (Str::startsWith($currentUrl, $url)) {
            return true;
        }

        $currentParts = array_filter(explode('/', parse_url($currentUrl, PHP_URL_PATH)));
        $urlParts = array_filter(explode('/', parse_url($url, PHP_URL_PATH)));

        if (count($urlParts) > 1) {
            array_pop($urlParts);
        }

        $currentParts = array_filter($currentParts, fn ($part) => ! is_numeric($part));
        $urlParts = array_filter($urlParts, fn ($part) => ! is_numeric($part));

        if (count($urlParts) > 3) {
            return Str::contains(implode('/', $currentParts), implode('/', $urlParts));
        }

        return false;
    }
}

if (! function_exists('extractQueryParams')) {
    function extractQueryParams($tabs)
    {
        return collect($tabs)
            ->map(function ($tab) {
                $queryString = parse_url($tab['link'], PHP_URL_QUERY);
                parse_str($queryString, $params);

                return array_keys($params);
            })
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }
}

if (! function_exists('isUrlActiveByParentKey')) {
    function isUrlActiveByParentKey(string $parent): bool
    {
        $currentUrl = request()->fullUrl();

        $without_origin = remove_origin($currentUrl, request()->root());

        $isSame = Str::contains($without_origin, $parent);

        if (! $isSame && Str::contains($parent, '-') && Str::contains($parent, 'settings') && Str::contains($without_origin, 'settings')) {
            $parent = explode('-', $parent)[0];
            $isSame = Str::contains($without_origin, $parent);
        }

        return $isSame;
    }
}

if (! function_exists('readJsonFile')) {
    function readJsonFile(string $path)
    {
        if (! $path) {
            return [];
        }

        $fullPath = str_starts_with($path, '/')
            ? $path
            : storage_path($path);

        $content = file_exists($fullPath) ? file_get_contents($fullPath) : '';

        return json_decode($content, true);
    }
}

if (! function_exists('remove_origin')) {
    function remove_origin($url, $origin = '/')
    {
        return str_replace($origin, '', $url);
    }
}

if (! function_exists('protectOnDemo')) {
    function protectOnDemo(mixed $original)
    {
        return env('APP_ENV') === 'demo' ? 'Protected for demo!' : $original;
    }
}

if (! function_exists('decodeUsername')) {
    function decodeUsername($username)
    {
        $username = base64_decode($username, true);
        $username = urldecode($username);
        $username = filter_var($username, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        return $username;
    }
}

if (! function_exists('getUrlWithQuery')) {
    function getUrlWithQuery(string $url, array $query): string
    {
        $parsedUrl = parse_url($url);
        $baseUrl = request()->url();

        $existingQuery = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $existingQuery);
        }

        $finalQuery = array_merge($query, $existingQuery);

        return $baseUrl.'?'.http_build_query($finalQuery);
    }
}
