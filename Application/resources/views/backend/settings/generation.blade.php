@extends('backend.layouts.form')
@section('section', admin_lang('Settings'))
@section('title', admin_lang('Image Generation'))
@section('container', 'container-max-lg')
@section('content')
    <form id="vironeer-submited-form" action="{{ route('admin.settings.generation.update') }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
            <div class="card-header">
                {{ admin_lang('Settings') }}
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-2">
                    <div class="col-12">
                        <label class="form-label">{{ admin_lang('Convert Original Image To WEBP') }} : <span
                                class="red">*</span></label>
                        <select name="image[original][webp_convert]" class="form-select">
                            <option value="0" @selected($settings->image->original->webp_convert == '0')>{{ admin_lang('No') }}</option>
                            <option value="1" @selected($settings->image->original->webp_convert == '1')>{{ admin_lang('Yes') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">{{ admin_lang('Generate image thumbnail') }} :</label>
                        <input id="generateImageThumbnail" type="checkbox" name="image[thumbnail][status]"
                            data-toggle="toggle" data-on="{{ admin_lang('Yes') }}" data-off="{{ admin_lang('No') }}"
                            @checked($settings->image->thumbnail->status)>
                    </div>
                </div>
                <div id="thumbnailOptions" class="mt-3 {{ !$settings->image->thumbnail->status ? 'd-none' : '' }}">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label">{{ admin_lang('Thumbnail Width') }} : <span
                                    class="red">*</span></label>
                            <input type="number" name="image[thumbnail][width]" class="form-control" min="1"
                                required value="{{ $settings->image->thumbnail->width }}">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">{{ admin_lang('Thumbnail Height') }} : <span
                                    class="red">*</span></label>
                            <input type="number" name="image[thumbnail][height]" class="form-control" min="1"
                                required value="{{ $settings->image->thumbnail->height }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ admin_lang('Convert Thumbnail To WEBP') }} : <span
                                    class="red">*</span></label>
                            <select name="image[thumbnail][webp_convert]" class="form-select">
                                <option value="0" @selected($settings->image->thumbnail->webp_convert == '0')>{{ admin_lang('No') }}</option>
                                <option value="1" @selected($settings->image->thumbnail->webp_convert == '1')>{{ admin_lang('Yes') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">{{ admin_lang('Watermark') }}</div>
            <div class="card-body p-4">
                <div class="vironeer-file-preview-box mb-3 bg-light py-4 text-center">
                    <div class="file-preview-box mb-3">
                        <img id="filePreview" src="{{ asset($settings->watermark->logo) }}"
                            width="{{ $settings->watermark->width }}px" height="{{ $settings->watermark->height }}px">
                    </div>
                    <button id="selectFileBtn" type="button"
                        class="btn btn-secondary mb-2">{{ admin_lang('Choose Image') }}</button>
                    <input id="selectedFileInput" type="file" name="watermark[logo]" accept="image/png" hidden>
                    <small class="text-muted d-block">{{ admin_lang('Image must be PNG format') }}</small>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">{{ admin_lang('Status') }} :</label>
                    <input type="checkbox" name="watermark[status]" data-toggle="toggle"
                        {{ $settings->watermark->status ? 'checked' : '' }}>
                </div>
                <div class="row g-3">
                    <div class="col-lg-12">
                        <label class="form-label">{{ admin_lang('Position') }} : <span class="red">*</span></label>
                        <select name="watermark[position]" class="form-select" required>
                            @foreach (\App\Models\Settings::WATERMARK_POSITIONS as $key => $value)
                                <option value="{{ $key }}"
                                    {{ $settings->watermark->position == $key ? 'selected' : '' }}>{{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Width') }} : <span class="red">*</span></label>
                        <input type="number" name="watermark[width]" class="form-control" min="25" max="1000"
                            value="{{ $settings->watermark->width }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Height') }} : <span class="red">*</span></label>
                        <input type="number" name="watermark[height]" class="form-control" min="25" max="1000"
                            value="{{ $settings->watermark->height }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ admin_lang('Add to') }} : <span class="red">*</span></label>
                        <select name="watermark[add_to]" class="form-select">
                            <option value="1" @selected($settings->watermark->add_to == 1)>
                                {{ admin_lang('Thumbnail') }}
                            </option>
                            <option value="2" @selected($settings->watermark->add_to == 2)>
                                {{ admin_lang('Original image') }}
                            </option>
                            <option value="3" @selected($settings->watermark->add_to == 3)>
                                {{ admin_lang('Thumbnail and original image') }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
