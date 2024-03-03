<?php

namespace App\Generators;

use App\Traits\InteractWithImageGeneration;
use Exception;
use GuzzleHttp\Client;

class StabilityAiGenerator
{
    use InteractWithImageGeneration;

    public function process($prompt, $negative_prompt, $samples, $size, $storageProvider, $model, $style = null)
    {
        try {
            $generatedImages = $this->generate($prompt, $negative_prompt, $samples, $size, $model, $style);
            $result = [];
            foreach ($generatedImages as $key => $image) {
                $result[$key] = $this->imageProcess($image, $storageProvider);
            }
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function generate($prompt, $negative_prompt, $samples, $size, $model, $style = null)
    {
        try {
            $apiProvider = apiProvider('stability-ai-stable-diffusion');
            $apiKey = $apiProvider->credentials->api_key;
            $size = explode('x', $size);
            $url = "https://api.stability.ai/v1/generation/{$model}/text-to-image";
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ];
            $body['steps'] = 50;
            $body['width'] = (integer) $size[0];
            $body['height'] = (integer) $size[1];
            $body['seed'] = 0;
            $body['cfg_scale'] = 7;
            $body['samples'] = (integer) $samples;
            if ($style) {
                $body['style_preset'] = $style;
            }
            $body['text_prompts'][] = [
                'text' => $prompt,
                'weight' => 1,
            ];

            if ($negative_prompt) {
                $body['text_prompts'][] = [
                    "text" => $negative_prompt,
                    "weight" => -1,
                ];
            }
            $client = new Client();
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $body,
            ]);
            $responseJSON = json_decode($response->getBody(), true);
            $images = [];
            foreach ($responseJSON['artifacts'] as $index => $image) {
                $images[$index] = base64_decode($image['base64']);
            }
            return $images;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}
