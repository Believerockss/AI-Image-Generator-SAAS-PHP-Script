<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Validator;

class GenerationController extends Controller
{
    public function index()
    {
        return view('backend.settings.generation');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image.original.webp_convert' => 'required|boolean',
            'image.thumbnail.width' => 'required_if:image.thumbnail.status,on|integer|min:1',
            'image.thumbnail.height' => 'required_if:image.thumbnail.status,on|integer|min:1',
            'image.thumbnail.webp_convert' => 'required_if:image.thumbnail.status,on|boolean',
            'watermark.logo' => 'nullable|image|mimes:png|max:2048',
            'watermark.position' => 'required|in:' . implode(',', array_keys(Settings::WATERMARK_POSITIONS)),
            'watermark.width' => 'required|integer|min:25|max:1000',
            'watermark.height' => 'required|integer|min:25|max:1000',
            'watermark.add_to' => 'required|integer|min:1|max:3',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                toastr()->error($error);
            }
            return back();
        }

        $requestData = $request->except('_token');

        $requestData['image']['thumbnail']['status'] = $request->has('image.thumbnail.status') ? 1 : 0;
        $requestData['watermark']['status'] = ($request->has('watermark.status')) ? 1 : 0;

        $size = $requestData['watermark']['width'] . 'x' . $requestData['watermark']['height'];

        if ($request->has('watermark.logo')) {
            $logo = imageUpload($request->file('watermark.logo'), 'images/watermark/', null, null, settings('watermark')->logo);
            $requestData['watermark']['logo'] = $logo;
        } else {
            $requestData['watermark']['logo'] = settings('watermark')->logo;
        }

        foreach ($requestData as $key => $value) {
            $update = Settings::updateSettings($key, $value);
            if (!$update) {
                toastr()->error(admin_lang(ucfirst($key) . ' ' . 'Updated Error'));
                return back();
            }
        }

        toastr()->success(admin_lang('Updated Successfully'));
        return back();

    }
}
