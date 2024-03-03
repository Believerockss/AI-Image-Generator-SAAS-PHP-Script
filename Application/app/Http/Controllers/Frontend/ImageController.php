<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Models\GeneratedImage;
use App\Models\StorageProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImageController extends Controller
{
    public function index()
    {
        $generatedImages = GeneratedImage::query();
        if (request()->filled('q')) {
            $q = '%' . request()->input('q') . '%';
            $generatedImages->where('prompt', 'like', $q)
                ->orWhere('negative_prompt', 'like', $q);
        }
        $generatedImages = $generatedImages->public()
            ->notExpired()
            ->orderByDesc('id')
            ->paginate(settings('limits')->explore_page_images);
        return view('frontend.images.index', compact('generatedImages'));
    }

    public function secure($id, $filename)
    {
        $generatedImage = GeneratedImage::where('id', unhashid($id))->where('filename', $filename)->notExpired()->firstOrFail();
        return $generatedImage->storageProvider->handler::getFile($generatedImage->path);
    }

    public function generator(Request $request)
    {
        $ip = ipInfo()->ip;
        if (demoMode()) {
            return response()->json(['error' => admin_lang('This version is for demo purpose, generating images are not allowed.')]);
        }
        if (!subscription()->is_subscribed) {
            return response()->json(['error' => lang('You need to have an active subscription to start generating the images', 'home page')]);
        }
        $apiProvider = apiProvider();
        if (!$apiProvider) {
            return response()->json(['error' => lang('API provider not enabled', 'home page')]);
        }
        $storageProvider = StorageProvider::where('alias', env('FILESYSTEM_DRIVER'))->first();
        if (!$storageProvider) {
            return jsonError(lang('Storage provider error', 'home page'));
        }
        $validator = Validator::make($request->all(), [
            'prompt' => ['required', 'string'],
            'negative_prompt' => ['nullable', 'string'],
            'model' => ['sometimes', 'integer'],
            'samples' => ['required', 'integer', 'min:1', 'max:' . $apiProvider->max],
            'size' => ['required', Rule::in(subscription()->plan->sizes)],
            'visibility' => ['sometimes', 'integer', 'min:0', 'max:1'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                return jsonError($error);
            }
        }
        if (!$apiProvider->supportNegativePrompt()) {
            $request->negative_prompt = null;
        }
        if (promptFilter($request->prompt) == true) {
            return jsonError(lang('Your prompt contains forbidden words', 'home page'));
        }
        if (subscription()->remaining_images < $request->samples || $request->samples > subscription()->plan->max_images) {
            if (Auth::user()) {
                return jsonError(lang('You have exceeded the limit, please upgrade your plan', 'home page'));
            } else {
                return jsonError(lang('You have exceeded the limit, please register', 'home page'));
            }
        }
        if (subscription()->plan->expiration) {
            $expiryAt = Carbon::now()->addDays(subscription()->plan->expiration);
        } else {
            $expiryAt = null;
        }
        $model = null;
        if ($apiProvider->hasModels()) {
            if ($request->has('model')) {
                if (!property_exists($apiProvider->models, $request->model)) {
                    return jsonError(lang('Invalid model', 'home page'));
                }
                $model = $apiProvider->models->{$request->model}->id;
            }
        }
        if ($apiProvider->hasStyles()) {
            if ($request->has('style')) {
                if (!array_key_exists($request->style, (array) $apiProvider->styles)) {
                    toastr()->error(lang('Invalid style', 'home page'));
                    return back();
                }
            }
            $style = $request->style;
        } else {
            $style = null;
        }
        try {
            $generator = new $apiProvider->generator;
            $generatedImages = $generator->process(
                $request->prompt,
                $request->negative_prompt,
                $request->samples,
                $request->size,
                $storageProvider,
                $model,
                $style
            );
            if (!is_array($generatedImages)) {
                return jsonError($generatedImages);
            }
            $images = [];
            foreach ($generatedImages as $key => $image) {
                $userId = Auth::user() ? Auth::user()->id : null;
                if (!$userId) {
                    $request->visibility = 1;
                }
                $generatedImage = GeneratedImage::create([
                    'user_id' => $userId,
                    'storage_provider_id' => $storageProvider->id,
                    'ip_address' => $ip,
                    'prompt' => $request->prompt,
                    'negative_prompt' => $request->negative_prompt,
                    'size' => $request->size,
                    'main' => $image['main'],
                    'thumbnail' => $image['thumbnail'],
                    'expiry_at' => $expiryAt,
                    'visibility' => $request->visibility,
                ]);
                if ($generatedImage) {
                    if (Auth::user()) {
                        Auth::user()->subscription->increment('generated_images');
                    }
                    $images[$key]['prompt'] = $generatedImage->prompt;
                    $images[$key]['src'] = $generatedImage->getThumbnailLink();
                    $images[$key]['link'] = route('images.show', hashid($generatedImage->id));
                    $images[$key]['download_link'] = route('images.download', [hashid($generatedImage->id), $generatedImage->getMainImageName()]);
                }
            }
            return response()->json(['images' => $images]);
        } catch (Exception $e) {
            return jsonError($e->getMessage());
        }
    }

    public function show($id)
    {
        $generatedImage = GeneratedImage::where('id', unhashid($id))->notExpired()->firstOrFail();
        if ($generatedImage->isPrivate()) {
            abort_if(auth()->user() && auth()->user()->id != $generatedImage->user_id, 404);
            abort_if(!auth()->user() && $generatedImage->user_id, 404);
            abort_if(!auth()->user() && $generatedImage->ip != ipInfo()->ip, 404);
        }
        $generatedImage->increment('views');
        return view('frontend.images.show', ['generatedImage' => $generatedImage]);
    }

    public function download($id)
    {
        $generatedImage = GeneratedImage::where('id', unhashid($id))->notExpired()->firstOrFail();
        if ($generatedImage->isPrivate()) {
            abort_if(auth()->user() && auth()->user()->id != $generatedImage->user_id, 404);
            abort_if(!auth()->user() && $generatedImage->user_id, 404);
            abort_if(!auth()->user() && $generatedImage->ip != ipInfo()->ip, 404);
        }
        if (!$this->authorizedUrl(route('images.show', hashid($generatedImage->id)))) {
            return redirect()->route('images.show', hashid($generatedImage->id));
        }
        $response = $generatedImage->download();
        if (!$response) {
            toastr()->error(lang('Download Error', 'image page'));
            return back();
        }
        $generatedImage->increment('downloads');
        return $response;
    }

    private function authorizedUrl($url)
    {
        $referer = request()->server('HTTP_REFERER');
        if ($referer && filter_var($referer, FILTER_VALIDATE_URL) !== false) {
            $referer = parse_url($referer);
            $url = parse_url($url);
            if ($url['host'] == $referer['host']) {
                return true;
            }
        }
        return false;
    }
}