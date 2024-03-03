@extends('backend.layouts.form')
@section('section', admin_lang('Settings'))
@section('title', admin_lang('Edit | ') . $apiProvider->name)
@section('back', route('admin.settings.api-providers.index'))
@section('container', 'container-max-lg')
@section('content')
    <form id="vironeer-submited-form" action="{{ route('admin.settings.api-providers.update', $apiProvider->id) }}"
        method="POST">
        @csrf
        <div class="card custom-card mb-4">
            <div class="card-body">
                <div class="vironeer-file-preview-box bg-light mb-3 p-4 text-center">
                    <div class="file-preview-box mb-3">
                        <img id="filePreview" src="{{ asset($apiProvider->logo) }}" height="100">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Name') }} : </label>
                        <input class="form-control" value="{{ $apiProvider->name }}" readonly>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ admin_lang('Status') }} :</label>
                        <input type="checkbox" name="status" data-toggle="toggle"
                            {{ $apiProvider->status ? 'checked' : '' }}>
                    </div>
                    @foreach ($apiProvider->credentials as $key => $value)
                        <div class="col-lg-12">
                            <label class="form-label capitalize">{{ str_replace('_', ' ', $key) }} :</label>
                            <input type="text" name="credentials[{{ $key }}]"
                                value="{{ demoMode() ? '' : $value }}" class="form-control remove-spaces">
                        </div>
                    @endforeach
                </div>
                {!! $apiProvider->instructions !!}
            </div>
        </div>
        <div class="card custom-card mb-4">
            <div class="card-header"><i class="fas fa-filter me-2"></i>{{ admin_lang('Filters') }}</div>
            <div class="card-body">
                <p>{{ admin_lang('Enter the words that you do not want to allow to be generated (bad words, sexual words, etc...)') }}
                </p>
                <input id="tagsInput" type="text" name="filters" class="form-control"
                    placeholder="{{ admin_lang('Enter the words') }}" value="{{ $apiProvider->filters }}">
            </div>
        </div>
        @if ($apiProvider->hasModels())
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $apiProvider->name . admin_lang(' Models') }}</span>
                    <button id="addModel" type="button" class="btn btn-dark"><i
                            class="fa fa-plus me-2"></i>{{ admin_lang('Add New Model') }}</button>
                </div>
                <ul id="models" class="list-group list-group-flush">
                    @if ($apiProvider->models)
                        @foreach ($apiProvider->models as $key => $model)
                            <li id="model_{{ $key }}" class="list-group-item p-3">
                                <div class="row g-2">
                                    <div class="col-12 col-xl">
                                        <input type="text" name="models[{{ $key }}][name]" class="form-control"
                                            placeholder="{{ admin_lang('Model name') }}" value="{{ $model->name }}"
                                            required>
                                    </div>
                                    <div class="col col-xl">
                                        <input type="text" name="models[{{ $key }}][id]"
                                            class="form-control remove-spaces" placeholder="{{ admin_lang('Model id') }}"
                                            value="{{ $model->id }}" required>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-danger remove-model"
                                            data-id="{{ $key }}"><i class="far fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif
    </form>
    @push('styles_libs')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tags-input/bootstrap-tagsinput.css') }}">
    @endpush
    @push('scripts_libs')
        <script src="{{ asset('assets/vendor/libs/tags-input/bootstrap-tagsinput.min.js') }}"></script>
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
                    if (!/^[a-zA-Z-0-9,]+$/.test(event.item)) {
                        event.cancel = true;
                        toastr.error('{{ admin_lang('Enter the filters without any symbols') }}');
                    }
                });
                let models = $('#models'),
                    addModel = $('#addModel'),
                    totalModels = "{{ $apiProvider->models ? count((array) $apiProvider->models) : 0 }}";
                addModel.on('click', function() {
                    totalModels++;
                    models.append('<li id="model_' + totalModels + '" class="list-group-item p-3">' +
                        '<div class="row g-2">' +
                        '<div class="col-12 col-xl">' +
                        '<input type="text" name="models[' + totalModels + '][name]" class="form-control"' +
                        'placeholder="{{ admin_lang('Model name') }}" required>' +
                        '</div>' +
                        '<div class="col col-xl">' +
                        '<input type="text" name="models[' + totalModels +
                        '][id]" class="form-control remove-spaces"' +
                        'placeholder="{{ admin_lang('Model id') }}" required>' +
                        '</div>' +
                        '<div class="col-auto">' +
                        '<button type="button" class="btn btn-danger remove-model" data-id="' +
                        totalModels + '">' +
                        '<i class="far fa-trash-alt"></i></button>' +
                        '</div>' +
                        '</div>' +
                        '</li>');
                    window.removeSpaces();
                    $('html, body').animate({
                        scrollTop: $(document).height()
                    }, 100);
                });
                $(document).on('click', '.remove-model', function() {
                    let id = $(this).data('id');
                    $('#model_' + id).remove();
                    totalModels--;
                });
            });
        </script>
    @endpush
@endsection
