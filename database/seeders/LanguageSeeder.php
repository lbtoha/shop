<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::insert([
            [
                'name' => 'English',
                'code' => 'en',
                'flag_code' => '🇺🇸',
                'language_file' => 'lang/en.json',
                'is_default' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Bangla',
                'code' => 'bn',
                'language_file' => 'lang/bn.json',
                'flag_code' => '🇧🇩',
                'is_default' => false,
                'status' => 'active',
            ],
        ]);
    }
}
