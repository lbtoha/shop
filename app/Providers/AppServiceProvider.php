<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton('flash', function () {
            return new \App\Services\Flash;
        });

        // Register macros for RedirectResponse
        RedirectResponse::macro('withSuccess', function ($message) {
            session()->flash('success', $message);

            return $this;
        });

        RedirectResponse::macro('withError', function ($message) {
            session()->flash('error', $message);

            return $this;
        });

        /**
         * Set Password Rules
         */
        Password::defaults(static function (): Password {
            return config('extra_service.system_config.force_secure_password.is_enabled') ? Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised() : Password::min(8);
        });

        /**
         * Filter by search term with wildcard search
         */
        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (\Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });

            return $this;
        });

        \Illuminate\Support\Facades\Blade::anonymousComponentNamespace('admin.components', 'admin');

        /**
         * Prefetch Vite assets
         */
        Vite::prefetch(3);

        // force HTTPS
        if (app()->environment('production') && config('extra_service.system_config.force_https.is_enabled')) {
            URL::forceScheme('https');
        }

    }
}
