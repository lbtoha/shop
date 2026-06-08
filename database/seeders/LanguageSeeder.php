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
            [
                'name' => 'Spanish',
                'code' => 'es',
                'flag_code' => '🇪🇸',
                'language_file' => 'lang/es.json',
                'is_default' => false,
                'status' => 'active',
            ],
            [
                'name' => 'French',
                'code' => 'fr',
                'flag_code' => '🇫🇷',
                'language_file' => 'lang/fr.json',
                'is_default' => false,
                'status' => 'active',
            ],
            [
                'name' => 'German',
                'code' => 'de',
                'flag_code' => '🇩🇪',
                'language_file' => 'lang/de.json',
                'is_default' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Arabic',
                'code' => 'ar',
                'flag_code' => '🇸🇦',
                'language_file' => 'lang/ar.json',
                'is_default' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Chinese',
                'code' => 'zh',
                'flag_code' => '🇨🇳',
                'language_file' => 'lang/zh.json',
                'is_default' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Hindi',
                'code' => 'hi',
                'flag_code' => '🇮🇳',
                'language_file' => 'lang/hi.json',
                'is_default' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Portuguese',
                'code' => 'pt',
                'flag_code' => '🇵🇹',
                'language_file' => 'lang/pt.json',
                'is_default' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Russian',
                'code' => 'ru',
                'flag_code' => '🇷🇺',
                'language_file' => 'lang/ru.json',
                'is_default' => false,
                'status' => 'active',
            ],
        ]);
    }
}
