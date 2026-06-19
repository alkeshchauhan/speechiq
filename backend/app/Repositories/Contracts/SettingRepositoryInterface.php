<?php

namespace App\Repositories\Contracts;

use App\Models\Setting;

interface SettingRepositoryInterface extends RepositoryInterface
{
    /**
     * Get a setting model by key.
     */
    public function getByKey(string $key): ?Setting;

    /**
     * Get a setting value directly.
     */
    public function getValue(string $key, mixed $default = null): mixed;

    /**
     * Update or create a setting key-value pair.
     */
    public function set(string $key, mixed $value, string $type = 'text', bool $isEncrypted = false): Setting;
}
