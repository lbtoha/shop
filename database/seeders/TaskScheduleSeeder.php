<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crons = [
            [
                'name' => __('Minute'),
                'interval' => '60',
            ],
            [
                'name' => __('Hourly'),
                'interval' => '3600',
            ],
            [
                'name' => __('Daily'),
                'interval' => '86400',
            ],
            [
                'name' => __('Monthly'),
                'interval' => '2592000',
            ],
            [
                'name' => __('Yearly'),
                'interval' => '31536000',
            ],

        ];

        foreach ($crons as $cron) {
            \App\Models\ScheduleTime::create($cron);
        }
    }
}
