<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::group(localizeOptions(), function () {
    Route::name('ipn.')->prefix('ipn')->namespace('Frontend\Gateways')->group(function () {
        Route::get('paypal_express', 'PaypalExpressController@ipn')->name('paypal_express');
        Route::get('stripe_checkout', 'StripeCheckoutController@ipn')->name('stripe_checkout');
        Route::get('mollie', 'MollieController@ipn')->name('mollie');
        Route::post('razorpay', 'RazorpayController@ipn')->name('razorpay');
    });
    Auth::routes(['verify' => true]);
    Route::namespace('Frontend')->group(function () {
        Route::get('cookie/accept', 'ExtraController@cookie')->middleware('ajax.only');
        Route::get('popup/close', 'ExtraController@popup')->middleware('ajax.only');
        Route::group(['namespace' => 'Auth'], function () {
            Route::get('login', 'LoginController@showLoginForm')->name('login');
            Route::post('login', 'LoginController@login');
            Route::get('login/{provider}', 'LoginController@redirectToProvider')->name('provider.login');
            Route::get('login/{provider}/callback', 'LoginController@handleProviderCallback')->name('provider.callback');
            Route::post('logout', 'LoginController@logout')->name('logout');
            Route::middleware(['disable.registration'])->group(function () {
                Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
                Route::post('register', 'RegisterController@register')->middleware('check.registration');
                Route::get('register/complete/{token}', 'RegisterController@showCompleteForm')->name('complete.registration');
                Route::post('register/complete/{token}', 'RegisterController@complete')->middleware('check.registration');
            });
            Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
            Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');
            Route::get('email/verify', 'VerificationController@show')->name('verification.notice');
            Route::post('email/verify/email/change', 'VerificationController@changeEmail')->name('change.email');
            Route::get('email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify');
            Route::post('email/resend', 'VerificationController@resend')->name('verification.resend');
        });
        Route::group(['namespace' => 'Auth', 'middleware' => ['auth', 'verified']], function () {
            Route::get('checkpoint/2fa/verify', 'CheckpointController@show2FaVerifyForm')->name('2fa.verify');
            Route::post('checkpoint/2fa/verify', 'CheckpointController@verify2fa');
        });
        Route::group(['prefix' => 'user', 'namespace' => 'User', 'middleware' => ['auth', 'verified', '2fa.verify']], function () {
            Route::get('/', function () {
                return redirect()->route('user.gallery.index');
            });
            Route::name('checkout.')->prefix('checkout')->group(function () {
                Route::get('{checkout_id}', 'CheckoutController@index')->name('index');
                Route::post('{checkout_id}/coupon/apply', 'CheckoutController@applyCoupon')->name('coupon.apply');
                Route::post('{checkout_id}/coupon/remove', 'CheckoutController@removeCoupon')->name('coupon.remove');
                Route::post('{checkout_id}/proccess', 'CheckoutController@proccess')->name('proccess');
            });
            Route::name('user.')->group(function () {
                Route::name('gallery.')->prefix('gallery')->group(function () {
                    Route::get('/', 'GalleryController@index')->name('index');
                    Route::post('{id}/update', 'GalleryController@update')->name('update');
                    Route::delete('{id}', 'GalleryController@destroy')->name('destroy');
                });
                Route::name('settings.')->prefix('settings')->group(function () {
                    Route::get('/', 'SettingsController@index')->name('index');
                    Route::post('details/update', 'SettingsController@detailsUpdate')->name('details.update');
                    Route::get('subscription', 'SettingsController@subscription')->name('subscription');
                    Route::get('payment-history', 'SettingsController@paymentHistory')->name('payment-history');
                    Route::get('password', 'SettingsController@password')->name('password');
                    Route::post('password/update', 'SettingsController@passwordUpdate')->name('password.update');
                    Route::get('2fa', 'SettingsController@towFactor')->name('2fa');
                    Route::post('2fa/enable', 'SettingsController@towFactorEnable')->name('2fa.enable');
                    Route::post('2fa/disabled', 'SettingsController@towFactorDisable')->name('2fa.disable');
                });
            });
        });
        Route::middleware(['verified', '2fa.verify'])->group(function () {
            Route::get('/', 'HomeController@index')->name('home');
            Route::name('images.')->prefix('images')->group(function () {
                Route::get('/explore', 'ImageController@index')->name('index');
                Route::post('generate', 'ImageController@generator')->name('generator');
                Route::get('{id}/view', 'ImageController@show')->name('show');
                Route::get('download/{id}/{name}', 'ImageController@download')->name('download');
            });
            Route::get('features', 'GlobalController@features')->name('features')->middleware('disable.features');
            Route::get('pricing', 'GlobalController@pricing')->name('pricing');
            Route::post('pricing/{id}/{type}', 'SubscribeController@subscribe')->name('subscribe');
            Route::name('blog.')->prefix('blog')->middleware('disable.blog')->group(function () {
                Route::get('/', 'BlogController@index')->name('index');
                Route::get('categories', 'BlogController@categories')->name('categories');
                Route::get('categories/{slug}', 'BlogController@category')->name('category');
                Route::get('articles', 'BlogController@articles');
                Route::get('articles/{slug}', 'BlogController@article');
                Route::post('articles/{slug}', 'BlogController@comment')->name('article');
            });
            Route::get('faqs', 'GlobalController@faqs')->name('faqs')->middleware('disable.faqs');
            Route::middleware('disable.contact')->group(function () {
                Route::get('contact-us', 'GlobalController@contact');
                Route::post('contact-us', 'GlobalController@contactSend')->name('contact');
            });
            if (env('VR_SYSTEMSTATUS') && !settings('actions')->language_type) {
                Route::get('{lang}', 'LocalizationController@localize')->where('lang', '^[a-z]{2}$')->name('localize');
            }
            Route::get('{slug}', 'GlobalController@page')->name('page');
        });
    });
});
