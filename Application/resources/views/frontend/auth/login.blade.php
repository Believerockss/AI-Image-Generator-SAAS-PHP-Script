@extends('frontend.layouts.auth')
@section('title', lang('Sign In', 'auth'))
@section('content')
    <div class="sign-box">
        <h4>{{ lang('Sign In', 'auth') }}</h4>
        <p class="text-muted fw-light mb-4">{{ lang('Sign in to your account to continue', 'auth') }}</p>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ lang('Email address', 'forms') }}</label>
                <input type="email" name="email" class="form-control form-control-md"
                    placeholder="{{ lang('Email address', 'forms') }}" value="{{ old('email') }}" required />
            </div>
            <div class="mb-3">
                <div class="row row-cols-auto justify-content-between flex-nowrap g-2">
                    <div class="col">
                        <label class="form-label">{{ lang('Password', 'forms') }}</label>
                    </div>
                    <div class="col">
                        <a href="{{ route('password.request') }}" class="link link-primary">
                            {{ lang('Forgot Your Password?', 'auth') }}
                        </a>
                    </div>
                </div>
                <input type="password" name="password" class="form-control form-control-md"
                    placeholder="{{ lang('Password', 'forms') }}" required />
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">{{ lang('Remember Me', 'auth') }}</label>
                </div>
            </div>
            {!! display_captcha() !!}
            <button class="btn btn-primary btn-md w-100">{{ lang('Sign In', 'auth') }}</button>
        </form>
        {!! facebook_login() !!}
    </div>
@endsection
