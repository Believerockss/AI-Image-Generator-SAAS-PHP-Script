<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use Illuminate\Http\Request;

class ApiProviderController extends Controller
{
    public function index()
    {
        $apiProviders = ApiProvider::all();
        return view('backend.settings.api-providers.index', ['apiProviders' => $apiProviders]);
    }

    public function edit(ApiProvider $apiProvider)
    {
        return view('backend.settings.api-providers.edit', ['apiProvider' => $apiProvider]);
    }

    public function update(Request $request, ApiProvider $apiProvider)
    {
        foreach ($request->credentials as $key => $value) {
            if (!array_key_exists($key, (array) $apiProvider->credentials)) {
                toastr()->error(admin_lang('Credentials parameter error'));
                return back();
            }
        }
        if ($request->has('status')) {
            foreach ($request->credentials as $key => $value) {
                if (empty($value)) {
                    toastr()->error(str_replace('_', ' ', $key) . admin_lang(' cannot be empty'));
                    return back();
                }
            }
            $request->status = 1;
        } else {
            if ($apiProvider->isDefault()) {
                toastr()->error(admin_lang('Default API Provider cannot be disabled'));
                return back();
            }
            $request->status = 0;
        }
        if ($request->has('models')) {
            if ($apiProvider->hasModels()) {
                foreach ($request->models as $key => $value) {
                    if (empty($value['name'])) {
                        toastr()->error(admin_lang('Model name cannot be empty'));
                        return back();
                    }
                    if (empty($value['id'])) {
                        toastr()->error(admin_lang('Model id cannot be empty'));
                        return back();
                    }
                }
            } else {
                $apiProvider->models = null;
            }
        }
        $apiProvider->credentials = $request->credentials;
        $apiProvider->filters = $request->filters;
        $apiProvider->models = $request->models;
        $apiProvider->status = $request->status;
        $apiProvider->save();
        toastr()->success(admin_lang('Updated Successfully'));
        return back();
    }

    public function setDefault(Request $request, ApiProvider $apiProvider)
    {
        abort_if($apiProvider->isDefault(), 401);
        if (!$apiProvider->status) {
            toastr()->error($apiProvider->name . ' ' . admin_lang('is disabled'));
            return back();
        }
        if ($apiProvider->hasModels() && !$apiProvider->models) {
            toastr()->error($apiProvider->name . ' ' . admin_lang('require models'));
            return back();
        }
        $apiProviders = ApiProvider::default()->get();
        foreach ($apiProviders as $provider) {
            $provider->is_default = false;
            $provider->save();
        }
        $apiProvider->is_default = true;
        $apiProvider->save();
        toastr()->success($apiProvider->name . ' ' . admin_lang('is now default API Provider'));
        return back();
    }
}
