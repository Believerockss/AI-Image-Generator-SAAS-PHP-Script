<?php

namespace App\Http\Controllers\Frontend\Storage;

use App\Http\Controllers\Controller;
use App\Traits\InteractWithFileStorage;
use Exception;
use Storage;

class StorjController extends Controller
{
    use InteractWithFileStorage;

    public function __construct()
    {
        $this->disk = Storage::disk('storj');
    }

    public static function setCredentials($data)
    {
        setEnv('STORJ_ACCESS_KEY_ID', $data->credentials->access_key_id);
        setEnv('STORJ_SECRET_ACCESS_KEY', $data->credentials->secret_access_key);
        setEnv('STORJ_DEFAULT_REGION', $data->credentials->default_region);
        setEnv('STORJ_BUCKET', $data->credentials->bucket);
        setEnv('STORJ_ENDPOINT', $data->credentials->endpoint);
        setEnv('STORJ_URL', $data->credentials->url);
    }

    public function upload($file, $path, $converted = false)
    {
        try {
            $filename = $this->generateUniqueFileName($converted);
            $path = $path . $filename;
            $this->disk->put($path, $file, 'public');
            return $this->response([
                'filename' => $filename,
                'path' => $path,
                'url' => $this->disk->url($path) . '?wrap=0',
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
                return redirect($this->disk->temporaryUrl($path, now()->addHour(), [
                    'ResponseContentDisposition' => 'attachment; filename="' . $generatedImage->getMainImageName() . '"',
                ]));
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