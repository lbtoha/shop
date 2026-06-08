<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
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
        try {
            $mailer = getOptionWithJsonDecode('email_config', []);
            if (empty($mailer)) {
                return;
            }
            config()->set('mail.default', $mailer['mailer']);
            switch ($mailer['mailer']) {
                case 'smtp':
                    config()->set('mail.mailers.smtp', [
                        'transport' => 'smtp',
                        'host' => $mailer['host'],
                        'port' => $mailer['port'],
                        'encryption' => $mailer['encryption'],
                        'username' => $mailer['username'],
                        'password' => $mailer['password'],
                        'timeout' => null,
                        'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
                    ]);

                    config()->set('mail.from', [
                        'address' => isset($mailer['mail_address']) ? $mailer['mail_address'] : 'no-reply@'.env('APP_URL', 'localhost'),
                        'name' => isset($mailer['mail_name']) ? $mailer['mail_name'] : 'Admin',
                    ]);

                    break;

                case 'mailgun':

                    config()->set('mail.mailers.mailgun', [
                        'transport' => 'mailgun',
                        'domain' => $mailer['domain'],
                        'secret' => $mailer['secret'],
                        'endpoint' => $mailer['endpoint'],
                    ]);

                    config()->set('mail.from', [
                        'address' => isset($mailer['mail_from']) ? $mailer['mail_from'] : 'no-reply@'.env('APP_URL', 'localhost'),
                        'name' => isset($mailer['mail_name']) ? $mailer['mail_name'] : 'Admin',
                    ]);

                    break;
            }

        } catch (\Throwable $th) {
            // throw $th;
        }
    }
}
