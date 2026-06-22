<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SettingService extends BaseService
{
    protected SettingRepositoryInterface $settingRepository;

    // Memory cache for runtime optimization
    protected static array $settingsCache = [];

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        parent::__construct($settingRepository);
        $this->settingRepository = $settingRepository;
    }

    /**
     * Get a setting value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$settingsCache)) {
            return self::$settingsCache[$key];
        }

        // Cache settings in Laravel cache for 1 hour to prevent DB overhead
        $value = Cache::remember('setting_' . $key, 3600, function () use ($key, $default) {
            return $this->settingRepository->getValue($key, $default);
        });

        self::$settingsCache[$key] = $value;
        return $value;
    }

    /**
     * Update or create a setting.
     */
    public function set(string $key, mixed $value, string $type = 'text', bool $isEncrypted = false): void
    {
        $this->settingRepository->set($key, $value, $type, $isEncrypted);

        // Clear caches
        Cache::forget('setting_' . $key);
        if (array_key_exists($key, self::$settingsCache)) {
            unset(self::$settingsCache[$key]);
        }
    }

    /**
     * Bulk update settings.
     */
    public function updateBulk(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $settingModel = $this->settingRepository->getByKey($key);
            if ($settingModel) {
                $type = $settingModel->setting_type;
                $isEncrypted = $settingModel->is_encrypted;

                // For password setting types, don't overwrite if it's masked or empty
                if ($type === 'password' && ($value === '********' || empty($value))) {
                    continue;
                }

                // Cast boolean to '1' or '0' string representation for db text field
                if ($type === 'boolean') {
                    $value = $value ? '1' : '0';
                }

                $this->set($key, $value, $type, $isEncrypted);
            }
        }
    }

    /**
     * Test connection to APIs.
     */
    public function testConnection(string $target, array $overrides = []): array
    {
        if ($target === 'gemini') {
            $apiKey = $overrides['GEMINI_API_KEY'] ?? null;
            $model = $overrides['GEMINI_MODEL'] ?? $this->get('GEMINI_MODEL', 'gemini-2.5-flash');

            if ($apiKey === '********' || $apiKey === null) {
                $apiKey = $this->get('GEMINI_API_KEY');
            }

            if (empty($apiKey)) {
                return ['success' => false, 'message' => 'Gemini API Key is empty. Enter a valid key to test.'];
            }

            try {
                $response = Http::withoutVerifying()
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            ['parts' => [['text' => 'ping']]]
                        ]
                    ]);

                if ($response->successful()) {
                    return ['success' => true, 'message' => 'Connection to Gemini successful!'];
                }

                $err = $response->json();
                $errMessage = $err['error']['message'] ?? 'Unknown Error';
                return [
                    'success' => false,
                    'message' => 'Gemini API Error: ' . $errMessage
                ];
            } catch (\Exception $e) {
                return ['success' => false, 'message' => 'Gemini Request Failed: ' . $e->getMessage()];
            }
        }

        if ($target === 'fastapi') {
            $apiUrl = $overrides['AI_API_URL'] ?? null;
            $apiToken = $overrides['AI_API_TOKEN'] ?? null;

            if ($apiUrl === null) {
                $apiUrl = $this->get('AI_API_URL');
            }
            if ($apiToken === '********' || $apiToken === null) {
                $apiToken = $this->get('AI_API_TOKEN');
            }

            if (empty($apiUrl)) {
                return ['success' => false, 'message' => 'AI Engine API URL is empty.'];
            }

            try {
                $request = Http::withoutVerifying()->timeout(5);
                if (!empty($apiToken)) {
                    $request = $request->withToken($apiToken);
                }

                $response = $request->post(rtrim($apiUrl, '/') . '/health-check');

                if ($response->successful()) {
                    return ['success' => true, 'message' => 'Connection to AI Engine successful!'];
                }

                return [
                    'success' => false,
                    'message' => 'AI Engine responded with HTTP status ' . $response->status()
                ];
            } catch (\Exception $e) {
                return ['success' => false, 'message' => 'AI Engine Request Failed: ' . $e->getMessage()];
            }
        }

        return ['success' => false, 'message' => 'Unknown test target.'];
    }
}
