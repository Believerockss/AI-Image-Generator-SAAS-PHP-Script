<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Backend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register backend routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::group(['middleware' => 'notInstalled', 'prefix' => adminPath(), 'namespace' => 'Backend'], function () {
    Route::name('admin.')->namespace('Auth')->group(function () {
        Route::get('/', 'LoginController@redirectToLogin')->name('index');
        Route::get('login', 'LoginController@showLoginForm')->name('login');
        Route::post('login', 'LoginController@login')->name('login.store');
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetLinkEmail');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.link');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.reset.change');
        Route::post('logout', 'LoginController@logout')->name('logout');
    });
    Route::group(['middleware' => 'admin'], function () {
        Route::name('admin.')->middleware('demo')->group(function () {
            Route::group(['prefix' => 'dashboard'], function () {
                Route::get('/', 'DashboardController@index')->name('dashboard');
                Route::get('charts/earnings', 'DashboardController@earningsChartData')->middleware('ajax.only');
                Route::get('charts/users', 'DashboardController@usersChartData')->middleware('ajax.only');
                Route::get('charts/logs', 'DashboardController@logsChartData')->middleware('ajax.only');
            });
            Route::name('notifications.')->prefix('notifications')->group(function () {
                Route::get('/', 'NotificationController@index')->name('index');
                Route::get('view/{id}', 'NotificationController@view')->name('view');
                Route::get('readall', 'NotificationController@readAll')->name('readall');
                Route::delete('deleteallread', 'NotificationController@deleteAllRead')->name('deleteallread');
            });
            Route::name('users.')->prefix('users')->group(function () {
                Route::post('{user}/edit/change/avatar', 'UserController@changeAvatar');
                Route::delete('{user}/edit/delete/avatar', 'UserController@deleteAvatar')->name('deleteAvatar');
                Route::post('{user}/edit/sentmail', 'UserController@sendMail')->name('sendmail');
                Route::get('{user}/edit/logs', 'UserController@logs')->name('logs');
                Route::get('{user}/edit/logs/get/{userLog}', 'UserController@getLogs')->middleware('ajax.only');
                Route::get('logs/{ip}', 'UserController@logsByIp')->name('logsbyip');
            });
            Route::resource('users', 'UserController');
            Route::name('images.')->prefix('images')->group(function () {
                Route::get('/', 'GeneratedImageController@index')->name('index');
                Route::get('{image}/download', 'GeneratedImageController@download')->name('download');
                Route::get('{image}/edit', 'GeneratedImageController@edit')->name('edit');
                Route::post('{image}/update', 'GeneratedImageController@update')->name('update');
                Route::delete('{image}', 'GeneratedImageController@destroy')->name('destroy');
                Route::post('multiDelete', 'GeneratedImageController@multiDelete')->name('multiDelete');
            });
            Route::resource('subscriptions', 'SubscriptionController');
            Route::resource('transactions', 'TransactionController');
            Route::resource('plans', 'PlanController');
            Route::resource('coupons', 'CouponController');
            Route::name('advertisements.')->prefix('advertisements')->group(function () {
                Route::get('/', 'AdvertisementController@index')->name('index');
                Route::get('{advertisement}/edit', 'AdvertisementController@edit')->name('edit');
                Route::post('{advertisement}', 'AdvertisementController@update')->name('update');
            });
        });
        Route::namespace('Navigation')->prefix('navigation')->name('admin.')->middleware('demo')->group(function () {
            Route::post('navbarMenu/nestable', 'NavbarMenuController@nestable')->name('navbarMenu.nestable');
            Route::resource('navbarMenu', 'NavbarMenuController');
            Route::post('footerMenu/nestable', 'FooterMenuController@nestable')->name('footerMenu.nestable');
            Route::resource('footerMenu', 'FooterMenuController');
        });
        Route::group(['prefix' => 'blog', 'namespace' => 'Blog', 'middleware' => ['demo', 'disable.blog']], function () {
            Route::get('categories/slug', 'CategoryController@slug')->name('categories.slug');
            Route::resource('categories', 'CategoryController');
            Route::get('articles/slug', 'ArticleController@slug')->name('articles.slug');
            Route::get('articles/categories/{lang}', 'ArticleController@getCategories')->middleware('ajax.only');
            Route::resource('articles', 'ArticleController');
            Route::get('comments', 'CommentController@index')->name('comments.index');
            Route::get('comments/{id}/view', 'CommentController@viewComment')->middleware('ajax.only');
            Route::post('comments/{id}/update', 'CommentController@updateComment')->name('comments.update');
            Route::delete('comments/{id}', 'CommentController@destroy')->name('comments.destroy');
        });
        Route::group(['prefix' => 'settings', 'namespace' => 'Settings', 'middleware' => 'demo', 'as' => 'admin.settings.'], function () {
            Route::get('general', 'GeneralController@index')->name('general');
            Route::post('general/update', 'GeneralController@update')->name('general.update');
            Route::get('generation', 'GenerationController@index')->name('generation');
            Route::post('generation/update', 'GenerationController@update')->name('generation.update');
            Route::name('api-providers.')->prefix('api-providers')->group(function () {
                Route::get('/', 'ApiProviderController@index')->name('index');
                Route::get('{apiProvider}/edit', 'ApiProviderController@edit')->name('edit');
                Route::post('{apiProvider}', 'ApiProviderController@update')->name('update');
                Route::post('{apiProvider}/default', 'ApiProviderController@setDefault')->name('default');
            });
            Route::name('storage.')->prefix('storage')->group(function () {
                Route::get('/', 'StorageController@index')->name('index');
                Route::get('edit/{storageProvider}', 'StorageController@edit')->name('edit');
                Route::post('edit/{storageProvider}', 'StorageController@update')->name('update');
                Route::post('connect/{storageProvider}', 'StorageController@storageTest')->name('test');
                Route::post('default/{storageProvider}', 'StorageController@setDefault')->name('default');
            });
            Route::name('smtp.')->prefix('smtp')->group(function () {
                Route::get('/', 'SmtpController@index')->name('index');
                Route::post('update', 'SmtpController@update')->name('update');
                Route::post('test', 'SmtpController@test')->name('test');
            });
            Route::name('storage.')->prefix('storage')->group(function () {
                Route::get('/', 'StorageController@index')->name('index');
                Route::get('edit/{storageProvider}', 'StorageController@edit')->name('edit');
                Route::post('edit/{storageProvider}', 'StorageController@update')->name('update');
                Route::post('connect/{storageProvider}', 'StorageController@storageTest')->name('test');
                Route::post('default/{storageProvider}', 'StorageController@setDefault')->name('default');
            });
            Route::name('extensions.')->prefix('extensions')->group(function () {
                Route::get('/', 'ExtensionController@index')->name('index');
                Route::get('{extension}/edit', 'ExtensionController@edit')->name('edit');
                Route::post('{extension}', 'ExtensionController@update')->name('update');
            });
            Route::name('gateways.')->prefix('gateways')->group(function () {
                Route::get('/', 'GatewayController@index')->name('index');
                Route::get('{gateway}/edit', 'GatewayController@edit')->name('edit');
                Route::post('{gateway}', 'GatewayController@update')->name('update');
            });
            Route::name('mailtemplates.')->prefix('mailtemplates')->group(function () {
                Route::get('/', 'MailTemplateController@index')->name('index');
                Route::post('settings/update', 'MailTemplateController@updateSettings')->name('updateSettings');
                Route::get('{mailTemplate}/edit', 'MailTemplateController@edit')->name('edit');
                Route::post('{mailTemplate}', 'MailTemplateController@update')->name('update');
            });
            Route::resource('taxes', 'TaxController');
            Route::get('pages/slug', 'PageController@slug')->name('pages.slug');
            Route::resource('pages', 'PageController');
            Route::resource('admins', 'AdminController');
            Route::name('languages.')->prefix('languages')->group(function () {
                Route::post('sort', 'LanguageController@sort')->name('sort');
                Route::get('translate/{code}', 'LanguageController@translate')->name('translates');
                Route::post('translate/{code}/export', 'LanguageController@export')->name('translates.export');
                Route::post('translate/{code}/import', 'LanguageController@import')->name('translates.import');
                Route::post('{id}/update', 'LanguageController@translateUpdate')->name('translates.update');
                Route::get('translate/{code}/{group}', 'LanguageController@translate')->name('translates.group');
            });
            Route::resource('languages', 'LanguageController');
            Route::resource('seo', 'SeoController');
        });

        Route::name('admin.')->middleware('demo')->group(function () {
            Route::name('extra.')->prefix('extra')->namespace('Extra')->group(function () {
                Route::get('custom-css', 'CustomCssController@index')->name('css');
                Route::post('custom-css/update', 'CustomCssController@update')->name('css.update');
                Route::get('popup-notice', 'PopupNoticeController@index')->name('notice');
                Route::post('popup-notice/update', 'PopupNoticeController@update')->name('notice.update');
            });
            Route::namespace('Others')->prefix('others')->group(function () {
                Route::resource('features', 'FeatureController');
                Route::resource('faqs', 'FaqController');
            });
            Route::post('ckeditor/upload', 'CKEditorController@upload');
            Route::name('system.')->namespace('System')->prefix('system')->group(function () {
                Route::get('info', 'InfoController@index')->name('info.index');
                Route::post('info/cache', 'InfoController@cache')->name('info.cache');
                Route::resource('plugins', 'PluginController')->except(['create', 'show']);
                Route::get('editor-files', 'EditorFileController@index')->name('editor-files.index');
                Route::delete('editor-files/{editorFile}', 'EditorFileController@destroy')->name('editor-files.destroy');
            });
            Route::namespace('Account')->prefix('account')->group(function () {
                Route::get('details', 'SettingsController@detailsForm')->name('account.details');
                Route::get('security', 'SettingsController@securityForm')->name('account.security');
                Route::post('details/update', 'SettingsController@detailsUpdate')->name('account.details.update');
                Route::post('security/update', 'SettingsController@securityUpdate')->name('account.security.update');
            });
        });
    });
});
