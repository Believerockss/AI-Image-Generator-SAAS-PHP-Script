<?php

namespace App\Http\Controllers\Frontend\Storage;

use App\Http\Controllers\Controller;
use App\Traits\InteractWithFileStorage;
use Exception;
use Storage;

class LocalController extends Controller
{
    use InteractWithFileStorage;

    public function __construct()
    {
        $this->disk = Storage::disk('direct');
    }

    public function upload($file, $path, $converted = false)
    {
        try {
            $filename = $this->generateUniqueFileName($converted);
            $path = $path . $filename;
            $this->disk->put($path, $file);
            return $this->response([
                'filename' => $filename,
                'path' => $path,
                'url' => url($path),
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function download($generatedImage)
    {
        try {
            $path = $generatedImage->getMainImagePath();
            if ($this->disk->has($path)) {
                return $this->disk->download($path);
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    public function delete($path)
    {
        if ($this->disk->has($path)) {
            $this->disk->delete($path);
        }
        return true;
    }
}