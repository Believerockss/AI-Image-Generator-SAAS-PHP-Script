<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public function index()
    {
        $extensions = Extension::all();
        return view('backend.settings.extensions.index', ['extensions' => $extensions]);
    }

    public function edit(Extension $extension)
    {
        return view('backend.settings.extensions.edit', ['extension' => $extension]);
    }

    public function update(Request $request, Extension $extension)
    {
        foreach ($request->credentials as $key => $value) {
            if (!array_key_exists($key, (array) $extension->credentials)) {
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
            $request->status = 0;
        }
        $update = $extension->update([
            'status' => $request->status,
            'credentials' => $request->credentials,
        ]);
        if ($update) {
            $extension->setCredentials();
            toastr()->success(admin_lang('Updated Successfully'));
            return back();
        }
    }
}