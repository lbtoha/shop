<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            LanguageSeeder::class,
            TaskScheduleSeeder::class,
            OptionSeeder::class,
            NotificationTemplateSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            ProductCatalogSeeder::class,
            BannerSeeder::class,
            HomeSectionSeeder::class,
        ]);
    }
}
