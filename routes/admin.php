<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\Auth;
use App\Http\Controllers\Admin\Extra;
use App\Http\Controllers\Admin\Notification\NotificationController;
use App\Http\Controllers\Admin\Settings\ServiceController;
use App\Http\Controllers\Admin\User;
use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\AdminGuestMiddleware;
use App\Http\Middleware\DemoMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/**
 * redirect to admin dashboard it the user hit the base url
 */
Route::middleware(AdminGuestMiddleware::class)->group(function () {
    Route::get('login', [Auth\AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [Auth\AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [Auth\NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [Auth\NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware([AdminAuthMiddleware::class])->group(function () {
    /**
     * auth routes
     */
    Route::post('logout', [Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
    Route::get('change-language/{code}', [Admin\Settings\Language\LanguageController::class, 'changeLang'])->name('change-language');
    /**---------------------- dashboard ---------------------- */

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::group(['prefix' => 'dashboard/overview', 'as' => 'dashboard.overview.'], function () {
        Route::get('login_log', [Admin\DashboardController::class, 'loginLogOverview'])->name('login_log');
        Route::get('login_log_browser', [Admin\DashboardController::class, 'loginLogBrowserOverview'])->name('login_log_browser');
        Route::get('login_log_by_day', [Admin\DashboardController::class, 'loginLogOverviewBYDay'])->name('login_log_by_day');
    });
    /** ---------------------- end dashboard ---------------------- */
    Route::group(['middleware' => [DemoMiddleware::class]], function () {
        /**---------------------- notifications ---------------------- */
        Route::resource('notifications', NotificationController::class)->except('create', 'edit', 'update');
        Route::get('get-latest-notification', [NotificationController::class, 'getLatestNotification'])->name('get-latest-notification');
        Route::get('delete-all-notification', [NotificationController::class, 'deleteAllNotification'])->name('delete-all-notification');
        Route::get('read-all-notification', [NotificationController::class, 'readAllNotification'])->name('read-all-notification');
        /** ---------------------- end notifications ---------------------- */

        /** -------------------------- Users -------------------------- */
        Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
            Route::post('deactivate/{user}', [User\UserController::class, 'deactivate'])->name('deactivate');
            Route::resource('notifications', User\NotificationController::class)->only(['index', 'store']);
            Route::get('ban/{user}', [User\UserController::class, 'banAccount'])->name('ban');
            Route::get('signin/{user}', [User\UserController::class, 'signInUser'])->name('signin');
        });
        Route::resource('users', User\UserController::class)->only(['index', 'edit', 'update', 'destroy']);
        /** -------------------------- End User USER -------------------------- */

        /** -------------------------- E-COMMERCE -------------------------- */
        Route::resource('categories', Admin\Category\CategoryController::class)->except(['show']);
        Route::resource('products', Admin\Product\ProductController::class)->except(['show']);
        Route::resource('banners', Admin\Banner\BannerController::class)->except(['show']);

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('/', [Admin\Order\OrderController::class, 'index'])->name('index');
            Route::get('export', [Admin\Order\OrderController::class, 'export'])->name('export');
            Route::get('{order}', [Admin\Order\OrderController::class, 'show'])->name('show');
            Route::get('{order}/invoice', [Admin\Order\OrderController::class, 'invoice'])->name('invoice');
            Route::put('{order}/status', [Admin\Order\OrderController::class, 'updateStatus'])->name('update-status');
            Route::put('{order}/advance', [Admin\Order\OrderController::class, 'advanceStatus'])->name('advance');
            Route::delete('{order}', [Admin\Order\OrderController::class, 'destroy'])->name('destroy');
        });
        /** -------------------------- End E-COMMERCE -------------------------- */

        /** -------------------------- ADMIN USER -------------------------- */
        Route::resource('admin-roles', Admin\AdminUser\RoleController::class);
        Route::resource('admins', Admin\AdminUser\UserController::class);
        Route::group(['prefix' => 'profile', 'as' => 'profile.', 'controller' => Admin\Settings\AdminProfileController::class], function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'update')->name('update');
            Route::post('change-password', [Admin\Settings\AdminProfileController::class, 'changePassword'])->name('change-password');
        });
        /** -------------------------- END ADMIN USER -------------------------- */

        /** -------------------------- SETTINGS -------------------------- */
        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
            Route::get('/', Admin\Settings\SettingController::class)->name('index');

            Route::prefix('system')->group(function () {
                /** -------------------------- APP -------------------------- */
                Route::group(['prefix' => 'app', 'as' => 'app.', 'controller' => Admin\Settings\AppController::class], function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/info', 'infoUpdate')->name('info.update');
                    Route::post('/otp', 'otpUpdate')->name('otp.update');
                });
                /** -------------------------- END APP -------------------------- */

                /** -------------------------- SYSTEM CONFIGURATION -------------------------- */
                Route::group(['prefix' => 'system-configurations', 'as' => 'system-configurations.', 'controller' => Admin\Settings\SystemConfigurationController::class], function () {
                    Route::get('pagination', 'pagination')->name('pagination.index');
                    Route::post('pagination', 'storePagination')->name('pagination.store');
                });
                Route::resource('system-configurations', Admin\Settings\SystemConfigurationController::class)->only(['index', 'store']);
                /** -------------------------- END SYSTEM CONFIGURATION -------------------------- */

                /** -------------------------- SCHEDULER -------------------------- */
                Route::resource('task-schedules', Admin\Settings\Schedule\TaskScheduleController::class)->except('create', 'edit');
                Route::controller(Admin\Settings\Schedule\TaskScheduleController::class)->group(function () {
                    Route::get('/task-schedule-status-change/{taskSchedule}', 'statusUpdate')->name('task-schedules.status-change');
                    Route::get('/run-now/{taskSchedule}', 'runNow')->name('task-schedules.run');
                    Route::get('/task-schedule/logs/{taskSchedule}', 'getLogs')->name('task-schedules.logs');
                    Route::get('/task-schedule/remove-logs/{taskSchedule}', 'removeLogs')->name('task-schedules.remove-logs');
                });
                Route::resource('schedule-times', Admin\Settings\Schedule\CronTimeController::class)->except('create', 'edit');
                /** -------------------------- END SCHEDULER -------------------------- */

                /** -------------------------- MAINTENANCE MODE SETTINGS -------------------------- */
                Route::resource('maintenance', Admin\Settings\MaintenanceController::class)->only(['index', 'store']);
                /** -------------------------- END MAINTENANCE MODE SETTINGS -------------------------- */

                /** -------------------------- SHOP SETTINGS -------------------------- */
                Route::resource('shop', Admin\Settings\ShopSettingController::class)->only(['index', 'store']);
                /** -------------------------- END SHOP SETTINGS -------------------------- */

                /** -------------------------- COOKIES POLICY -------------------------- */
                Route::resource('gdpr-cookies', Admin\Settings\CookieController::class)->only(['index', 'store']);
                /** -------------------------- END COOKIES POLICY -------------------------- */
            });

            Route::prefix('user')->group(function () {
                /** -------------------------- NOTIFICATION SETTINGS -------------------------- */
                Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
                    Route::resource('templates', Admin\Settings\Notification\NotificationTemplateController::class)->except(['index', 'show']);
                    Route::group(['controller' => Admin\Settings\Notification\ServicesController::class], function () {
                        Route::get('services', 'index')->name('services');
                        Route::post('services/{service}', 'store')->name('services.store');
                        Route::post('test/email', 'testEmailService')->name('test.email');
                        Route::get('preview/email/{templateBody}', 'previewEmail')->name('preview.email');
                    });
                });
                Route::get('change-status/{notificationTemplate}', [Admin\Settings\Notification\NotificationTemplateController::class, 'changeStatus'])->name('notification-templates.change-status');
                /** -------------------------- END NOTIFICATION TEMPLATE -------------------------- */
            });

            Route::prefix('theme')->group(function () {
                /** -------------------------- PWA CONFIG -------------------------- */
                Route::resource('pwa', Admin\Settings\PwaConfigController::class);

                /** -------------------------- LOGO & FAVICON -------------------------- */
                Route::resource('logo-favicon', Admin\Settings\LogoFaviconController::class)->only(['index', 'store']);
            });

            Route::prefix('integration')->group(function () {
                /** -------------------------- CUSTOM CSS -------------------------- */
                Route::resource('services', ServiceController::class)->only(['index', 'store']);

                /** -------------------------- EXTENSION -------------------------- */
                Route::controller(Admin\Settings\ExtensionController::class)->group(function () {
                    Route::get('extensions', 'index')->name('extensions.index');
                    Route::put('extensions/{slug}', 'update')->name('extensions.update');
                    Route::get('extensions/{slug}/enable', 'enable')->name('extensions.enable');
                });
                /** -------------------------- END EXTENSION -------------------------- */
            });

            Route::prefix('localization')->group(function () {
                /** -------------------------- LANGUAGES -------------------------- */
                Route::group(['prefix' => 'languages', 'as' => 'languages.'], function () {
                    Route::get('/download-help-json', [Admin\Settings\Language\LanguageController::class, 'downloadHelpJson'])->name('download-help-json');
                    Route::controller(Admin\Settings\Language\LanguageJsonController::class)->group(function () {
                        Route::get('/json/edit/{language}', 'index')->name('json.edit');
                        Route::post('/json/store/{language}', 'store')->name('json.store');
                        Route::put('/json/update/{language}', 'update')->name('json.update');
                        Route::delete('/json/destroy/{language}', 'destroy')->name('json.destroy');
                    });
                });
                Route::resource('languages', Admin\Settings\Language\LanguageController::class)->except('create');
                /** -------------------------- END LANGUAGES -------------------------- */
            });

            Route::prefix('navigation')->group(function () {
                /** -------------------------- MENU -------------------------- */
                Route::resource('menus', Admin\Settings\Menu\MenuController::class)->except(['create', 'show']);
                Route::get('menus/{menu}/change-status', [Admin\Settings\Menu\MenuController::class, 'changeStatus'])->name('menus.change-status');
                Route::group(['prefix' => 'menus', 'as' => 'menus.', 'controller' => Admin\Settings\Menu\MenuItemController::class], function () {
                    Route::post('/{menu}/item/store', 'store')->name('single.item.store');
                    Route::post('/{menu}/item/delete', 'destroy')->name('single.item.destroy');
                    Route::post('/{menu}/item/update', 'update')->name('single.item.update');
                    Route::post('/{menu}/item/bulk-update', 'bulkMenuItemUpdate')->name('bulk.item.update');
                });
                /** -------------------------- END MENU -------------------------- */
            });

            /** -------------------------- SEO -------------------------- */
            Route::prefix('seo')->name('seo.')->controller(Admin\Settings\SeoConfigController::class)->group(function () {
                Route::get('/', 'index')->name('index.page');
                Route::post('/', 'update')->name('update');
                Route::get('/sitemap', 'sitemap')->name('sitemap');
                Route::post('/sitemap', 'sitemapUpdate')->name('sitemap.update');
                Route::get('/generate-sitemap', 'generateSitemap')->name('generate.sitemap');
                Route::get('/robots', 'robots')->name('robots');
                Route::post('/robots', 'robotsUpdate')->name('robots.update');
                Route::get('/generate-robots', 'generateRobots')->name('generate.robots');
            });
            /** -------------------------- END SEO -------------------------- */

            /** -------------------------- ADMIN USER -------------------------- */
            Route::controller(Admin\Settings\AdminProfileController::class)->group(function () {
                Route::get('/profile', 'index')->name('profile');
                Route::post('/profile', 'update')->name('profile.update');
                Route::post('/profile/change-password', 'changePassword')->name('profile.change-password');
            });
        });
        /** -------------------------- END SETTINGS -------------------------- */
    });

    /** -------------------------- EXTRAS -------------------------- */
    Route::group(['prefix' => 'extras', 'as' => 'extras.'], function () {
        Route::get('application-info', [Extra\ApplicationInfoController::class, 'index'])->name('application-info');
        Route::get('cache/{command}', [Extra\CacheController::class, 'cacheClear'])->name('cache');
        Route::get('clear-frontend-cache', [Extra\CacheController::class, 'clearFrontendCache'])->name('clear-frontend-cache');
        Route::get('clear-cache', [Extra\CacheController::class, 'clearAll'])->name('clear-all');

        // application update routes
        Route::get('update', [Extra\ApplicationUpdateController::class, 'index'])->name('application-update');
        Route::post('check-for-update', [Extra\ApplicationUpdateController::class, 'store'])->name('check-for-update');
    });
    /** -------------------------- END EXTRAS -------------------------- */
});

/* -------------------------- FILEMANAGER -------------------------- */
Route::group(['prefix' => 'filemaneger', 'as' => 'filemaneger.', 'middleware' => ['auth:admin']], function () {
    try {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    } catch (\Throwable $th) {
        // throw $th;
    }
});
/* -------------------------- END FILEMANAGER -------------------------- */

/* -------------------------- Helper STORAGE LINK Route-------------------------- */
Route::get('storage-link', function () {
    Artisan::call('storage:link');

    return response()->json(['message' => __('Storage linked successfully')]);
});
