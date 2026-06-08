<?php

namespace Database\Seeders;

use App\Enums\NotificationType;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (NotificationType::cases() as $case) {
            $title = ucwords(str_replace('_', ' ', $case->value));

            $template = \App\Models\NotificationTemplate::create([
                'name' => $title,
                'type' => $case->value,
                'short_codes' => $case->shortcodes(),
                'is_auto' => true,
            ]);
            $is_default = $case->value == 'default';
            $is_active = in_array($case->value, ['default', 'password_reset', 'verify_email', 'order_placed', 'order_status_updated']);

            $template->bodies()->createMany([
                [
                    'channel' => 'sms',
                    'subject' => $title,
                    'body' => $is_default ? '{{body}}' : '{{name}} {{email}} {{phone}}',
                    'is_active' => $is_active,
                ],
                [
                    'channel' => 'push',
                    'subject' => $title,
                    'body' => $is_default ? '{{body}}' : '{{name}} {{email}} {{phone}}',
                    'is_active' => $is_active,
                ],
                [
                    'channel' => 'email',
                    'subject' => $title,
                    'body' => $case === NotificationType::DEFAULT ? $this->defaultTemplateBody() : implode(' ', array_keys($case->shortcodes())),
                    'is_active' => $is_active,
                ],
            ]);
        }
    }

    private function defaultTemplateBody(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333333; background-color: #f4f4f4;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 0;">
                <table role="presentation" style="width: 600px; border-collapse: collapse; background-color: #ffffff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 20px 0; background-color: #00b300; text-align: center;">
                            <h1 style="margin: 0; font-size: 24px; line-height: 1.2; color: #ffffff;">
                                {{subject}}
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px;">
                            {{body}}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; text-align: center; font-size: 14px; color: #666666;">
                            <p style="margin: 0;">© 2023 <a href="{{site_url}}" target="_blank" rel="noopener noreferrer">{{site_name}}</a>. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
HTML;
    }
}
