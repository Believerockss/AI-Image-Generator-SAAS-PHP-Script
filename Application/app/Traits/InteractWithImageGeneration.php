<?php

namespace App\Traits;

use App\Http\Methods\ImageToWebp;
use App\Traits\InteractWithFileStorage;
use GuzzleHttp\Client;
use Intervention\Image\Facades\Image;

trait InteractWithImageGeneration
{
    use InteractWithFileStorage;

    public function imageProcess($image, $storageProvider)
    {
        $imageSettings = settings('image');

        if ($imageSettings->thumbnail->status) {
            $thumbnail = $this->generateImageThumbnail($image);
            $thumbnail = $this->addWatermarkToImage($thumbnail, 'thumbnail');
            $data['thumbnail']['converted'] = false;
            if ($imageSettings->thumbnail->webp_convert) {
                $thumbnail = $this->convertImageToWebp($thumbnail);
                $data['thumbnail']['converted'] = true;
            }
            $data['thumbnail']['image'] = $thumbnail;
        }

        $image = $this->addWatermarkToImage($image);

        $data['main']['converted'] = false;
        if ($imageSettings->original->webp_convert) {
            $image = $this->convertImageToWebp($image);
            $data['main']['converted'] = true;
        }

        $data['main']['image'] = $image;

        $response = $this->uploadImageResources($data, $storageProvider);

        return $response;
    }

    public function uploadImageResources($data, $storageProvider)
    {
        $handler = new $storageProvider->handler;

        $response = [];

        $mainResponse = $handler->upload($data['main']['image'], 'generation/images/', $data['main']['converted']);
        $response['main']['filename'] = $mainResponse->filename;
        $response['main']['path'] = $mainResponse->path;
        $response['main']['url'] = $mainResponse->url;

        $response['thumbnail'] = null;
        if (isset($data['thumbnail'])) {
            $thumbnailResponse = $handler->upload($data['thumbnail']['image'], 'generation/images/thumbnails/', $data['thumbnail']['converted']);
            $response['thumbnail']['filename'] = $thumbnailResponse->filename;
            $response['thumbnail']['path'] = $thumbnailResponse->path;
            $response['thumbnail']['url'] = $thumbnailResponse->url;
        }

        return $response;
    }

    protected function generateImageThumbnail($image)
    {
        $width = settings('image')->thumbnail->width;
        $height = settings('image')->thumbnail->height;

        $image = Image::make($image);

        if ($image->width() != $width && $image->height() != $height) {
            $image->resize($width, $height);
        }

        return $image->encode();
    }

    protected function convertImageToWebp($image)
    {
        $imageToWebp = new ImageToWebp();
        return $imageToWebp->convert($image);
    }

    protected function addWatermarkToImage($image, $type = 'main')
    {
        $watermarkSettings = settings('watermark');

        $watermark = false;

        if ($watermarkSettings->status && subscription()->plan->watermark) {

            if ($type = 'main' && in_array($watermarkSettings->add_to, [2, 3])) {
                $watermark = true;
            }

            if ($type = 'thumbnail' && in_array($watermarkSettings->add_to, [1, 3])) {
                $watermark = true;
            }

        }

        if ($watermark) {
            $watermark = Image::make($watermarkSettings->logo);
            $generatedImage = Image::make($image);

            $watermark->resize($watermarkSettings->width, $watermarkSettings->height);
            $generatedImage->insert($watermark, $watermarkSettings->position, 5, 5);

            return $generatedImage->encode();
        }

        return $image;
    }

    protected function downloadImage($imageUrl)
    {
        $client = new Client();
        $response = $client->get($imageUrl);

        return $response->getBody();
    }

}