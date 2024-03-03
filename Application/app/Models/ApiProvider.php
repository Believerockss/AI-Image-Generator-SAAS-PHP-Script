<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiProvider extends Model
{
    use HasFactory;

    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    public function scopeDefault($query)
    {
        $query->where('is_default', 1);
    }

    public function isDefault()
    {
        return $this->is_default == 1;
    }

    public function hasModels()
    {
        return $this->has_models;
    }

    public function hasStyles()
    {
        return $this->styles != null;
    }

    public function supportNegativePrompt()
    {
        return $this->support_negative_prompt;
    }

    protected $fillable = [
        'credentials',
        'models',
        'filters',
        'is_default',
        'status',
    ];

    protected $casts = [
        'credentials' => 'object',
        'models' => 'object',
        'styles' => 'object',
    ];

}
