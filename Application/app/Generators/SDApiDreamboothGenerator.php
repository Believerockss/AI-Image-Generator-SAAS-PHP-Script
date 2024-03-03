<?php

namespace App\Generators;

use App\Traits\InteractWithImageGeneration;
use Exception;
use GuzzleHttp\Client;

class SDApiDreamboothGenerator
{
    use InteractWithImageGeneration;

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://modelslab.com/api/v6/images/',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function process($prompt, $negative_prompt, $samples, $size, $storageProvider, $model)
    {
        try {
            $generatedImages = $this->generate($prompt, $negative_prompt, $samples, $size, $model);
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

    private function generate($prompt, $negative_prompt, $samples, $size, $model)
    {
        try {
            $apiProvider = apiProvider('stable-diffusion-api-dreambooth');
            $apiKey = $apiProvider->credentials->api_key;
            $size = explode('x', $size);
            $body['key'] = $apiKey;
            $body['model_id'] = $model;
            $body['prompt'] = $prompt;
            if ($negative_prompt) {
                $body['negative_prompt'] = $negative_prompt;
            }
            $body['width'] = $size[0];
            $body['height'] = $size[1];
            $body['samples'] = $samples;
            $body['num_inference_steps'] = '30';
            $body['guidance_scale'] = 7.5;
            $response = $this->client->post('text2img', [
                'json' => $body,
            ]);
            $response = json_decode($response->getBody(), true);
            $imageUrls = null;
            if ($response['status'] == "success") {
                $imageUrls = $response['output'];
            } else {
                $retries = 0;
                while ($response['status'] == 'processing' && $retries < 10) {
                    sleep(5);
                    $fetchData = $this->client->post('fetch/', [
                        'json' => [
                            'key' => $apiKey,
                            'request_id' => $response['id'],
                        ],
                    ]);
                    $output = json_decode($fetchData->getBody(), true);
                    if ($output['status'] == 'success') {
                        $imageUrls = $output['output'];
                    }
                    $retries++;
                }
            }
            return $imageUrls;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
