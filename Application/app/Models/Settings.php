<?php

namespace App\Models;

use App\Http\Methods\UnicodeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Settings extends UnicodeModel
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'value' => 'object',
    ];

    public static function selectSettings($key)
    {
        $setting = Settings::where('key', $key)->first();
        if ($setting) {
            return $setting->value;
        }
        return false;
    }

    public static function updateSettings($key, $value)
    {
        $setting = Settings::where('key', $key)->first();
        if ($setting) {
            if (count((array) $setting->value) == count((array) $value)) {
                $setting->value = $value;
                return $setting->save();
            }
        }
        return false;
    }

    public const WATERMARK_POSITIONS = [
        'top-left' => 'Top Left',
        'top' => 'Top',
        'top-right' => 'Top Right',
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right',
        'bottom-left' => 'Bottom Left',
        'bottom' => 'Bottom',
        'bottom-right' => 'Bottom Right',
    ];
}