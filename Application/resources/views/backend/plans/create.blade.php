@extends('backend.layouts.form')
@section('title', admin_lang('Create a new plan'))
@section('container', 'container-max-lg')
@section('back', route('admin.plans.index'))
@section('content')
    <form id="vironeer-submited-form" action="{{ route('admin.plans.store') }}" method="POST">
        @csrf
        <div class="card custom-card mb-4">
            <div class="card-header bg-primary text-white">
                {{ admin_lang('Plan details') }}
            </div>
            <ul id="customFeatures" class="custom-list-group list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-lg-6">
                            <label class="col-form-label"><strong>{{ admin_lang('Plan Name') }} : <span
                                        class="red">*</span></strong></label>
                        </div>
                        <div class="col-12 col-lg-2">
                            <input type="checkbox" name="is_featured" class="form-check-input">
                            <label>{{ admin_lang('Featured plan') }}</label>
                        </div>
                        <div class="col col-lg-4">
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}"
                                placeholder="{{ admin_lang('Enter plan name') }}" autofocus>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-lg-8">
                            <label class="col-form-label d-block"><strong>{{ admin_lang('Short Description') }} : <span
                                        class="red">*</span></strong></label>
                        </div>
                        <div class="col-12 col-lg-4">
                            <textarea name="short_description" class="form-control" required placeholder="{{ admin_lang('Max 150 character') }}">{{ old('short_description') }}</textarea>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-lg-8">
                            <label class="col-form-label"><strong>{{ admin_lang('Plan Interval') }} : <span
                                        class="red">*</span></strong></strong></label>
                        </div>
                        <div class="col col-lg-4">
                            <select name="interval" class="form-select" required>
                                <option value="1" {{ old('interval') == 1 ? 'selected' : '' }}>
                                    {{ admin_lang('Monthly') }}
                                </option>
                                <option value="2" {{ old('interval') == 2 ? 'selected' : '' }}>
                                    {{ admin_lang('Yearly') }}
                                </option>
                            </select>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-lg-6">
                            <label class="col-form-label"><strong>{{ admin_lang('Plan Price') }} : <span
                                        class="red">*</span></strong></strong></label>
                        </div>
                        <div class="col-12 col-lg-2">
                            <input type="checkbox" name="is_free" class="free-plan-checkbox form-check-input">
                            <label>{{ admin_lang('Free') }}</label>
                        </div>
                        <div class="col col-lg-4">
                            <div class="custom-input-group input-group plan-price">
                                <input type="text" name="price" class="form-control input-price"
                                    value="{{ old('price') }}" placeholder="0.00" required />
                                <span class="input-group-text"><strong>{{ $settings->currency->code }}</strong></span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="plan-require-login list-group-item d-none">
                    <div class="row align-items-center">
                        <div class="col-8 col-lg-8">
                            <label class="col-form-label d-block"><strong>{{ admin_lang('Require Login') }} :
                                </strong></label>
                            <small>{{ admin_lang('Without login, the guests will be able to generate the images.') }}</small>
                        </div>
                        <div class="col-4 col-lg-4">
                            <input type="checkbox" name="login_require" data-toggle="toggle"
                                data-on="{{ admin_lang('Yes') }}" data-off="{{ admin_lang('No') }}"
                                {{ old('login_require') ? 'checked' : '' }}>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-lg-8">
                            <label class="col-form-label d-block"><strong>{{ admin_lang('Total Images') }} : <span
                                        class="red">*</span></strong></label>
                            <small>{{ admin_lang('Total images that users or guests can generate every month or year.') }}</small>
                        </div>
                        <div class="col-12 col-lg-4">
                            <input type="number" name="images" class="form-control" placeholder="0"
                                value="{{ old('images') }}" min="1" required>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-lg-8">
                            <label class="col-form-label d-block"><strong>{{ admin_lang('Max Images For Every Request') }}
                                    : <span class="red">*</span></strong></label>
                            <small>
                                {{ admin_lang('How many images can be generated on every request.') }}
                                @if (apiProvider())
                                    ({{ apiProvider()->name . ' ' . admin_lang('Max images is') . ' ' . apiProvider()->max }})
                                @endif
                            </small>
                        </div>
                        <div class="col-12 col-lg-4">
                            <input type="number" name="max_images" class="form-control" placeholder="0"
                                value="{{ old('max_images') }}" min="1"
                                max="{{ apiProvider() ? apiProvider()->max : '' }}" required>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-lg-6">
                            <label class="col-form-label d-block"><strong>{{ admin_lang('Expiration') }} : <span
                                        class="red">*</span></strong></strong></label>
                            <small>{{ admin_lang('When will generated images expire.') }}</small>
                        </div>
                        <div class="col-12 col-lg-2">
                            <input type="checkbox" name="no_expiration" class="form-check-input plan-expiration">
                            <label>{{ admin_lang('Unlimited time') }}</label>
                        </div>
                        <div class="col col-lg-4">
                            <div class="custom-input-group input-group plan-expiration-input">
                                <input type="text" name="expiration" class="form-control"
                                    value="{{ old('expiration') }}" placeholder="0" required />
                                <span class="input-group-text"><strong>{{ admin_lang('Days') }}</strong></span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-lg-7">
                            <label class="col-form-label d-block"><strong>{{ admin_lang('Image Sizes') }}
                                    : <span class="red">*</span></strong></label>
                            <small>{{ admin_lang('Enter the allowed sizes for this plan for example (512x512, 1024x1024, etc...) the size must be supported by the API provider and it must be in format (WidthxHeight) in max (4000x4000) or it will not work.') }}</small>
                        </div>
                        <div class="col-12 col-lg-5">
                            <input id="tagsInput" type="text" name="sizes" class="form-control"
                                placeholder="{{ admin_lang('Enter the sizes') }}">
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-8 col-lg-8">
                            <label class="col-form-label d-block"><strong>{{ admin_lang('Show advertisements') }} :
                                </strong></label>
                            <small>{{ admin_lang('Show the advertisements (Yes/No)') }}</small>
                        </div>
                        <div class="col-4 col-lg-4">
                            <input type="checkbox" name="advertisements" data-toggle="toggle"
                                data-on="{{ admin_lang('Yes') }}" data-off="{{ admin_lang('No') }}"
                                {{ old('advertisements') ? 'checked' : '' }}>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-8 col-lg-8">
                            <label class="col-form-label d-block"><strong>{{ admin_lang('Watermark') }} :
                                </strong></label>
                            <small>{{ admin_lang('Add watermark in generated images (Yes/No)') }}</small>
                        </div>
                        <div class="col-4 col-lg-4">
                            <input type="checkbox" name="watermark" data-toggle="toggle"
                                data-on="{{ admin_lang('Yes') }}" data-off="{{ admin_lang('No') }}"
                                {{ old('watermark') ? 'checked' : '' }}>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <button type="button" id="addCustomFeature" class="btn btn-dark"><i
                class="fa fa-plus me-2"></i>{{ admin_lang('Add custom feature') }}</button>
    </form>
    @push('styles_libs')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tags-input/bootstrap-tagsinput.css') }}">
    @endpush
    @push('scripts_libs')
        <script src="{{ asset('assets/vendor/libs/jquery/jquery.priceformat.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/tags-input/bootstrap-tagsinput.min.js') }}"></script>
    @endpush
    @push('top_scripts')
        <script>
            "use strict";
            var customFeatureI = -1;
        </script>
    @endpush
    @push('scripts')
        <script>
            "use strict";
            $(function() {
                let tagsInput = $('#tagsInput');
                tagsInput.tagsinput({
                    cancelConfirmKeysOnEmpty: false
                });
                tagsInput.on('beforeItemAdd', function(event) {
                    if (!/^(25[6-9]|[3-9]\d{2}|[1-3]\d{3}|4000)x(25[6-9]|[3-9]\d{2}|[1-3]\d{3}|4000)$/
                        .test(event
                            .item)) {
                        event.cancel = true;
                        toastr.error('{{ admin_lang('The size format is invalid') }}');
                    }
                });
            });
        </script>
    @endpush
@endsection
