<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FlyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadOptions();
    }

    private function loadOptions()
    {

        try {

            $app_info = getOptionWithJsonDecode('company_info', config('application_info'));

            config()->set('application_info.company_info', $app_info['company_info']);
            config()->set('app.timezone', $app_info['timezone']);
            config()->set('app.locale', getOption('default_language', config('app.locale')));
            config()->set('application_info.frontend_url', $app_info['frontend_url']);
            config()->set('application_info.theme', $app_info['theme'] ?? config('application_info.theme'));

            config()->set('application_info.address', $app_info['address']);
            config()->set('application_info.logo_favicon', getOptionWithJsonDecode('logo_favicon', config('application_info.logo_favicon')));

            config()->set('application_info.otp', $app_info['otp']);
            config()->set('application_info.referral', $app_info['referral']);
            config()->set('application_info.footer_text', $app_info['footer_text']);
            config()->set('application_info.footer_menu_id', $app_info['footer_menu_id'] ?? null);
            config()->set('application_info.auth_left_sidebar_image', $app_info['auth_left_sidebar_image']);
            config()->set('application_info.mobile_app', $app_info['mobile_app'] ?? config('application_info.mobile_app'));
            config()->set('app.mobile_app_key', $app_info['mobile_app_key'] ?? config('app.mobile_app_key'));

            config()->set('application_info.social_medias', getOptionWithJsonDecode('social_medias', config('application_info.social_medias')));
            config()->set('seo', getOptionWithJsonDecode('seo_meta', config('seo')));
            config()->set('pwa', getOptionWithJsonDecode('pwa_config', config('pwa')));
            config()->set('sitemap', getOptionWithJsonDecode('sitemap', config('sitemap')));
            config()->set('robots.rules', getOptionWithJsonDecode('robots_rules', config('robots.rules')));

            config()->set('extension.google_analytics', getOptionWithJsonDecode(key: 'extension_google_analytics', default: config('extension.google_analytics')));
            config()->set('extension.recaptcha', getOptionWithJsonDecode(key: 'extension_recaptcha', default: config('extension.recaptcha')));
            config()->set('extra_service.system_config', getOptionWithJsonDecode(key: 'extra_service_system_config', default: config('extra_service.system_config')));
            config()->set('extra_service.site_pagination_config', getOptionWithJsonDecode(key: 'extra_service_pagination', default: config('extra_service.pagination')));
            config()->set('extra_service.cookie_consent', getOptionWithJsonDecode(key: 'gdpr_cookies', default: config('extra_service.cookie_consent')));

            /**
             * socialite providers
             */
            foreach (config('application_info.auth_providers') as $key => $value) {
                $provider = getOptionWithJsonDecode('socialite_providers_'.$value['id'], null);

                if ($provider) {
                    config()->set('services.'.$value['id'], $provider['service']);
                    config()->set('application_info.auth_providers.'.$key.'.is_enabled', $provider['is_enabled']);
                }
            }
        } catch (\Throwable $th) {

        }
    }
}
