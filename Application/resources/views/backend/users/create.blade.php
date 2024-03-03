@extends('backend.layouts.form')
@section('section', admin_lang('Users'))
@section('title', admin_lang('Add new user'))
@section('container', 'container-max-lg')
@section('back', route('admin.users.index'))
@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            {{ admin_lang('User details') }}
        </div>
        <div class="card-body">
            <form id="vironeer-submited-form" action="{{ route('admin.users.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="avatar text-center py-4">
                    <img id="filePreview" src="{{ asset('images/avatars/default.png') }}" class="rounded-circle mb-3"
                        width="140" height="140">
                    <button id="selectFileBtn" type="button"
                        class="btn btn-secondary d-flex btn-lg m-auto">{{ admin_lang('Choose Image') }}</button>
                    <input id="selectedFileInput" type="file" name="avatar" accept="image/png, image/jpg, image/jpeg"
                        hidden>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">{{ admin_lang('First Name') }} : <span class="red">*</span></label>
                            <input type="firstname" name="firstname" class="form-control form-control-lg" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">{{ admin_lang('Last Name') }} : <span class="red">*</span></label>
                            <input type="lastname" name="lastname" class="form-control form-control-lg" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ admin_lang('E-mail Address') }} : <span class="red">*</span></label>
                    <input type="email" name="email" class="form-control form-control-lg" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">{{ admin_lang('Password') }} : <span class="red">*</span></label>
                    <input type="text" name="password" class="form-control form-control-lg" value="{{ $password }}"
                        required>
                </div>
            </form>
        </div>
    </div>
@endsection
