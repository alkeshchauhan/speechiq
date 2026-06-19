<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get the setting value, decrypting if necessary.
     */
    public function getSettingValueAttribute($value)
    {
        if ($this->is_encrypted && !empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value; // Fallback to raw if decryption fails
            }
        }

        return $value;
    }

    /**
     * Set the setting value, encrypting if necessary.
     */
    public function setSettingValueAttribute($value)
    {
        if ($this->is_encrypted && !empty($value)) {
            try {
                // Check if already encrypted (to avoid double encryption)
                Crypt::decryptString($value);
                $this->attributes['setting_value'] = $value;
            } catch (\Exception $e) {
                $this->attributes['setting_value'] = Crypt::encryptString($value);
            }
        } else {
            $this->attributes['setting_value'] = $value;
        }
    }
}
