<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedImage extends Model
{
    use HasFactory;

    public const VISIBILITY_PUBLIC = 1;
    public const VISIBILITY_PRIVATE = 0;

    public function scopePublic($query)
    {
        return $query->where('visibility', self::VISIBILITY_PUBLIC);
    }

    public function isPublic()
    {
        return $this->visibility == self::VISIBILITY_PUBLIC;
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', self::VISIBILITY_PRIVATE);
    }

    public function isPrivate()
    {
        return $this->visibility == self::VISIBILITY_PRIVATE;
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_at')
            ->where('expiry_at', '<', Carbon::now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expiry_at')
                ->orWhere('expiry_at', '>', Carbon::now());
        });
    }

    public function scopeUsers($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeGuests($query)
    {
        return $query->whereNull('user_id');
    }

    protected $fillable = [
        'user_id',
        'storage_provider_id',
        'ip_address',
        'prompt',
        'negative_prompt',
        'size',
        'main',
        'thumbnail',
        'expiry_at',
        'visibility',
    ];

    protected $casts = [
        'main' => 'object',
        'thumbnail' => 'object',
        'expiry_at' => 'datetime',
    ];

    public function getMainImageLink()
    {
        return $this->main->url;
    }

    public function getMainImageName()
    {
        return $this->main->filename;
    }

    public function getMainImagePath()
    {
        return $this->main->path;
    }

    public function getThumbnailLink()
    {
        $source = $this->main->url;
        if ($this->thumbnail) {
            $source = $this->thumbnail->url;
        }
        return $source;
    }

    public function download()
    {
        $handler = new $this->storageProvider->handler;
        return $handler->download($this);
    }

    public function deleteResources()
    {
        $handler = new $this->storageProvider->handler;
        $handler->delete($this->main->path);
        if ($this->thumbnail) {
            $handler->delete($this->thumbnail->path);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function storageProvider()
    {
        return $this->belongsTo(StorageProvider::class);
    }
}