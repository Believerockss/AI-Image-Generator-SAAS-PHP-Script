<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\GeneratedImage;
use Illuminate\Http\Request;

class GeneratedImageController extends Controller
{
    public function index()
    {
        $unviewedGeneratedImages = GeneratedImage::where('is_viewed', 0)->get();
        if ($unviewedGeneratedImages->count() > 0) {
            foreach ($unviewedGeneratedImages as $unviewedGeneratedImage) {
                $unviewedGeneratedImage->is_viewed = 1;
                $unviewedGeneratedImage->save();
            }
        }
        $widget['guests_images'] = GeneratedImage::guests()->notExpired()->count();
        $widget['users_images'] = GeneratedImage::users()->notExpired()->count();
        if (request()->has('search') && !is_null(request('search'))) {
            $q = request()->input('search');
            $query = GeneratedImage::notExpired()->where('prompt', 'like', '%' . $q . '%');
        } else {
            $query = GeneratedImage::notExpired();
        }
        if (request()->has('user')) {
            $query = $query->where('user_id', request('user'));
        }
        $generatedImages = $query->orderbyDesc('id')->paginate(20);
        $generatedImages->appends(['search' => $q ?? '']);
        return view('backend.Images.index', ['generatedImages' => $generatedImages, 'widget' => $widget]);
    }

    public function edit(GeneratedImage $image)
    {
        return view('backend.Images.edit', ['generatedImage' => $image]);
    }

    public function update(Request $request, GeneratedImage $image)
    {
        $request->visibility = ($request->has('visibility')) ? 1 : 0;
        $image->visibility = $request->visibility;
        $image->update();
        toastr()->success(admin_lang('Updated Successfully'));
        return back();
    }

    public function download(GeneratedImage $image)
    {
        $response = $image->download();
        if (!$response) {
            toastr()->error(admin_lang('Download Error'));
            return back();
        }
        return $response;
    }

    public function multiDelete(Request $request)
    {
        if (empty($request->delete_ids)) {
            toastr()->error(admin_lang('You have not selected any Image'));
            return back();
        }
        try {
            $ImagesIds = explode(',', $request->delete_ids);
            $totalDelete = 0;
            foreach ($ImagesIds as $ImagesId) {
                $generatedImage = GeneratedImage::where('id', $ImagesId)->notExpired()->first();
                if (!is_null($generatedImage)) {
                    $generatedImage->deleteResources();
                    $generatedImage->delete();
                    $totalDelete += 1;
                }
            }
            if ($totalDelete != 0) {
                $countFiles = ($totalDelete > 1) ? $totalDelete . ' ' . admin_lang('Images') : $totalDelete . ' ' . admin_lang('Image');
                toastr()->success($countFiles . ' ' . admin_lang('deleted successfully'));
                return back();
            } else {
                toastr()->info(admin_lang('No files have been deleted'));
                return back();
            }
        } catch (\Exception $e) {
            toastr()->error($e->getMessage());
            return back();
        }
    }

    public function destroy(GeneratedImage $image)
    {
        $image->deleteResources();
        $image->delete();
        toastr()->success(admin_lang('Deleted successfully'));
        return redirect()->route('admin.images.index');
    }
}
