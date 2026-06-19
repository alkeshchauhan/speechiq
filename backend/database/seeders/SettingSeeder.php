<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove obsolete OpenAI and USE_GEMINI settings from the database
        $obsoleteKeys = [
            'OPENAI_API_KEY',
            'OPENAI_MODEL',
            'OPENAI_TTS_MODEL',
            'OPENAI_TRANSCRIBE_MODEL',
            'USE_GEMINI',
        ];
        Setting::whereIn('setting_key', $obsoleteKeys)->delete();

        $settings = [
            [
                'setting_key' => 'AI_API_URL',
                'setting_value' => 'http://127.0.0.1:8001',
                'setting_type' => 'text',
                'is_encrypted' => false
            ],
            [
                'setting_key' => 'AI_API_TOKEN',
                'setting_value' => '',
                'setting_type' => 'password',
                'is_encrypted' => true
            ],
            [
                'setting_key' => 'ENABLE_AI_INTERVIEW',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'is_encrypted' => false
            ],
            [
                'setting_key' => 'ENABLE_READ_ALOUD',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'is_encrypted' => false
            ],
            [
                'setting_key' => 'ENABLE_TTS',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'is_encrypted' => false
            ],
            [
                'setting_key' => 'ENABLE_STT',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'is_encrypted' => false
            ],
            [
                'setting_key' => 'GEMINI_API_KEY',
                'setting_value' => '',
                'setting_type' => 'password',
                'is_encrypted' => true
            ],
            [
                'setting_key' => 'GEMINI_MODEL',
                'setting_value' => 'gemini-1.5-flash',
                'setting_type' => 'text',
                'is_encrypted' => false
            ],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['setting_key' => $s['setting_key']], $s);
        }
    }
}
