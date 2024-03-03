<?php

namespace App\Http\Methods;

use App\Traits\InteractWithFileStorage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use WebPConvert\WebPConvert;

class ImageToWebp
{
    use InteractWithFileStorage;

    public function convert($image)
    {
        $imageName = $this->generateUniqueFileName(true);
        $imageDestination = storage_path("app/temp/{$imageName}");

        $image = $this->storageImageUpload($image);
        $imageSource = storage_path("app/{$image}");

        WebPConvert::convert($imageSource, $imageDestination);

        File::delete($imageSource);

        $image = Image::make($imageDestination);

        $encodedImage = $image->encode();

        File::delete($imageDestination);

        return $encodedImage;
    }

    protected function storageImageUpload($image)
    {
        $disk = Storage::disk('local');

        $imageName = $this->generateUniqueFileName();
        $path = "temp/{$imageName}";

        $disk->put($path, $image);

        return $path;
    }
}