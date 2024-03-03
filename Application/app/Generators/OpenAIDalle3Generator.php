<?php

namespace App\Generators;

use App\Traits\InteractWithImageGeneration;
use Exception;
use GuzzleHttp\Client;

class OpenAIDalle3Generator
{
    use InteractWithImageGeneration;

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . apiProvider('openai-dalle-3')->credentials->api_key,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function process($prompt, $negative_prompt, $samples, $size, $storageProvider, $model = null, $style = null)
    {
        try {
            $generatedImages = $this->generate($prompt, $samples, $size, $model, $style);
            $result = [];
            foreach ($generatedImages as $key => $image) {
                $image = $this->downloadImage($image);
                $result[$key] = $this->imageProcess($image, $storageProvider);
            }
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function generate($prompt, $samples, $size, $model = null, $style = null)
    {
        try {
            $body = [
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'quality' => 'hd',
                'n' => $samples,
                'size' => $size,
            ];
            if ($style) {
                $body['style'] = $style;
            }
            $response = $this->client->post('images/generations', [
                'json' => $body,
            ]);
            $response = json_decode($response->getBody(), true);
            $imageUrls = [];
            foreach ($response['data'] as $image) {
                $imageUrls[] = $image['url'];
            }
            return $imageUrls;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
