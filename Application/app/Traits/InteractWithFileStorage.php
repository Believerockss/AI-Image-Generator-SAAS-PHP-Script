<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait InteractWithFileStorage
{
    public function generateUniqueFileName($converted = false)
    {
        $fileExtension = $converted ? 'webp' : $this->getImageExtension();
        $filename = Str::random(15) . '_' . time();
        $filename = $filename . '.' . strtolower($fileExtension);
        return $filename;
    }

    public function response($response)
    {
        return json_decode(json_encode($response));
    }

    protected function getImageExtension()
    {
        if (apiProvider()->alias == "openai") {
            return 'jpg';
        }
        return 'png';
    }
}