@extends('frontend.user.layouts.app')
@section('title', lang('Settings', 'account'))
@section('container', 'dash-container-small')
@section('content')
    <div class="settings">
        <div class="row g-3">
            @include('frontend.user.includes.settings-sidebar')
            <div class="col-lg-8 col-xxl-9">
                <div class="card-v p-0">
                    <div class="settings-box">
                        <div class="settings-box-header border-bottom px-4 py-4">
                            <h6 class="mb-0">{{ lang('Account details', 'account') }}</h6>
                        </div>
                        <div class="settings-box-body p-4">
                            <form id="deatilsForm" action="{{ route('user.settings.details.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input id="change_avatar" type="file" name="avatar"
                                    accept="image/jpg, image/jpeg, image/png" hidden />
                                <div class="row row-cols-1 row-cols-sm-2 g-3 mb-3">
                                    <div class="col">
                                        <label class="form-label">{{ lang('First Name', 'forms') }}</label>
                                        <input type="firstname" name="firstname" class="form-control form-control-md"
                                            placeholder="{{ lang('First Name', 'forms') }}" maxlength="50"
                                            value="{{ $user->firstname }}" required>
                                    </div>
                                    <div class="col">
                                        <label class="form-label">{{ lang('Last Name', 'forms') }}</label>
                                        <input type="lastname" name="lastname" class="form-control form-control-md"
                                            placeholder="{{ lang('Last Name', 'forms') }}" maxlength="50"
                                            value="{{ $user->lastname }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ lang('Email address', 'forms') }}</label>
                                    <input type="email" name="email" class="form-control form-control-md"
                                        placeholder="{{ lang('Email address', 'forms') }}" value="{{ $user->email }}"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ lang('Address line 1', 'forms') }}</label>
                                    <input type="text" name="address_1" class="form-control form-control-md"
                                        value="{{ @$user->address->address_1 }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ lang('Address line 2', 'forms') }} :</label>
                                    <input type="text" name="address_2" class="form-control form-control-md"
                                        placeholder="{{ lang('Apartment, suite, etc. (optional)', 'account') }}"
                                        value="{{ @$user->address->address_2 }}">
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('City', 'forms') }}</label>
                                            <input type="text" name="city" class="form-control form-control-md"
                                                value="{{ @$user->address->city }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('State', 'forms') }}</label>
                                            <input type="text" name="state" class="form-control form-control-md"
                                                value="{{ @$user->address->state }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ lang('Postal code', 'forms') }}</label>
                                            <input type="text" name="zip" class="form-control form-control-md"
                                                value="{{ @$user->address->zip }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">{{ lang('Country', 'forms') }}</label>
                                    <select name="country" class="form-select form-select-md" required>
                                        @foreach (countries() as $country)
                                            <option value="{{ $country->id }}"
                                                {{ $country->name == @$user->address->country ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-primary btn-md">{{ lang('Save Changes', 'account') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
