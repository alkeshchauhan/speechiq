<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function getByKey(string $key): ?Setting
    {
        return $this->model->where('setting_key', $key)->first();
    }

    public function getValue(string $key, mixed $default = null): mixed
    {
        $setting = $this->getByKey($key);
        if ($setting) {
            $val = $setting->setting_value;
            if ($setting->setting_type === 'boolean') {
                return filter_var($val, FILTER_VALIDATE_BOOLEAN);
            }
            return $val;
        }
        return $default;
    }

    public function set(string $key, mixed $value, string $type = 'text', bool $isEncrypted = false): Setting
    {
        return $this->model->updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_type' => $type,
                'is_encrypted' => $isEncrypted,
            ]
        );
    }
}
