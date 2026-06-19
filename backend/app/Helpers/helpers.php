<?php

use App\Services\SettingService;

if (!function_exists('setting')) {
    /**
     * Get setting value by key or retrieve setting service instance.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return app(SettingService::class);
        }

        return app(SettingService::class)->get($key, $default);
    }
}
