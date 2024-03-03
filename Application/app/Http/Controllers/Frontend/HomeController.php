<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GeneratedImage;

class HomeController extends Controller
{
    public function index()
    {
        $generatedImages = GeneratedImage::public()
            ->notExpired()
            ->limit(settings('limits')->home_page_images)
            ->orderbyDesc('id')->get();
        $apiProvider = apiProvider();
        return view('frontend.home', [
            'generatedImages' => $generatedImages,
            'apiProvider' => $apiProvider,
        ]);
    }
}
