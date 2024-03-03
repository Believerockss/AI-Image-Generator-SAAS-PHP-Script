<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    use HasFactory;

    protected $fillable = [
        'credentials',
        'status',
    ];

    protected $casts = [
        'credentials' => 'object',
    ];

    public function setCredentials()
    {
        if ($this->alias == "google_recaptcha") {
            setEnv('NOCAPTCHA_SITEKEY', $this->credentials->site_key);
            setEnv('NOCAPTCHA_SECRET', $this->credentials->secret_key);
        } elseif ($this->alias == "facebook_oauth") {
            setEnv('FACEBOOK_CLIENT_ID', $this->credentials->client_id);
            setEnv('FACEBOOK_CLIENT_SECRET', $this->credentials->client_secret);
        }
    }
}