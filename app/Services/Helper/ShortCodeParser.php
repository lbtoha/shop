<?php

namespace App\Services\Helper;

use App\Enums\NotificationType;
use App\Models\NotificationTemplate;

class ShortCodeParser
{
    /**
     * @return string
     */
    public static function parse(string $content, $model = null, $data = [])
    {
        if (count($data)) {
            $content = str_replace(array_map(fn (string $key) => '{{'.$key.'}}', array_keys($data)), array_values($data), $content);
        }

        $data = [];

        if ($model && $model instanceof \Illuminate\Database\Eloquent\Model) {
            $data = $model->toArray();
        }

        if (is_array($model)) {
            $data = $model;
        }

        if (empty($data)) {
            return $content;
        }

        $filter_value = array_filter($data, fn ($value) => is_scalar($value) && ! is_null($value));
        $content = str_replace(array_map(fn (string $key) => '{{'.$key.'}}', array_keys($filter_value)), array_values($filter_value), $content);
        // name key
        $full_name = $data['full_name'] ?? null;
        if ($full_name) {
            $content = str_replace('{{name}}', $full_name, $content);
        }

        return $content;
    }

    /**
     * Replace short codes in an email body, using the default email template, with the given body and model/data.
     *
     * @param  mixed  $model
     * @param  array  $data
     * @return string
     */
    public static function emailBodyParse(string $body, ?string $subject = null, $model = null, $data = [])
    {
        $defaultTemplate = NotificationTemplate::where('type', NotificationType::DEFAULT)->first();

        if (! $defaultTemplate) {
            return self::parse($body, $model, $data);
        }

        $default_email_template = $defaultTemplate->bodies()->where('channel', 'email')->first();

        if (! $default_email_template) {
            return self::parse($body, $model, $data);
        }

        $default_short_codes = [
            '{{subject}}' => $subject ?? 'Hi there',
            '{{body}}' => self::parse($body, $model, $data),
            '{{site_name}}' => config('application_info.company_info.name'),
            '{{site_url}}' => config('app.url'),
        ];

        return str_replace(array_keys($default_short_codes), array_values($default_short_codes), $default_email_template->body);
    }

    public static function smsDefaultBodyParse(string $body, $model = null, $data = [])
    {
        $template = NotificationTemplate::where('type', NotificationType::DEFAULT)->first();

        if (! $template) {
            return self::parse($body, $model, $data);
        }

        $default_sms_template = $template->bodies()->where('channel', 'sms')->first();

        if (! $default_sms_template) {
            return self::parse($body, $model, $data);
        }

        $default_short_codes = [
            '{{body}}' => self::parse($body, $model, $data),
        ];

        return str_replace(array_keys($default_short_codes), array_values($default_short_codes), $default_sms_template->body);
    }

    public static function pushDefaultBodyParse(string $body, $model = null, $data = [])
    {
        $template = NotificationTemplate::where('type', NotificationType::DEFAULT)->first();

        if (! $template) {
            return self::parse($body, $model, $data);
        }

        $default_push_template = $template->bodies()->where('channel', 'push')->first();

        if (! $default_push_template) {
            return self::parse($body, $model, $data);
        }

        $default_short_codes = [
            '{{body}}' => self::parse($body, $model, $data),
        ];

        return str_replace(array_keys($default_short_codes), array_values($default_short_codes), $default_push_template->body);
    }
}
