<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Methods\ReCaptchaValidation;
use App\Models\Country;
use App\Models\SocialProvider;
use App\Models\User;
use App\Models\UserLog;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::USER;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new log
     *
     * @return // save data
     */
    public function createLog($user)
    {
        $newLoginLog = new UserLog();
        $newLoginLog->user_id = $user->id;
        $newLoginLog->ip = ipInfo()->ip;
        $newLoginLog->country = ipInfo()->location->country;
        $newLoginLog->country_code = ipInfo()->location->country_code;
        $newLoginLog->timezone = ipInfo()->location->timezone;
        $newLoginLog->location = ipInfo()->location->city . ', ' . ipInfo()->location->country;
        $newLoginLog->latitude = ipInfo()->location->latitude;
        $newLoginLog->longitude = ipInfo()->location->longitude;
        $newLoginLog->browser = ipInfo()->system->browser;
        $newLoginLog->os = ipInfo()->system->os;
        $newLoginLog->save();
    }

    /**
     * Create a new admin notification
     *
     * @return // save data
     */
    public function createAdminNotify($user)
    {
        $title = $user->name . ' ' . admin_lang('has registered');
        $image = asset($user->avatar);
        $link = route('admin.users.edit', $user->id);
        return adminNotify($title, $image, $link);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        return view('frontend.auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['sometimes', 'required'],
        ] + ReCaptchaValidation::validate());
    }

    /**
     * Before register a new user
     *
     * @return //redirect
     */
    public function register(Request $request)
    {
        $data = $request->all();
        $this->validator($data)->validate();
        $user = $this->create($data);
        event(new Registered($user));
        $this->guard()->login($user);
        return $this->registered($request, $user)
        ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['firstname'] . ' ' . $data['lastname'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'avatar' => 'images/avatars/default.png',
            'password' => Hash::make($data['password']),
        ]);
        if ($user) {
            $this->createAdminNotify($user);
            $this->createLog($user);
        }
        return $user;
    }

    /**
     * Show the application complete registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showCompleteForm(Request $request, $token)
    {
        abort_if($token != session('provider_data'), 401);
        $data = decrypt(session('provider_data'));
        return view('frontend.auth.complete', ['data' => $data, 'token' => $token]);
    }

    /**
     * Create a new user instance after a valid complete registration.
     *
     * @param  string  $token
     * @return \App\Models\User
     */
    public function complete(Request $request, $token)
    {
        abort_if($token != session('provider_data'), 401);
        $this->validator($request->all())->validate();
        $user = User::create([
            'name' => $request->firstname . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'avatar' => 'images/avatars/default.png',
            'password' => Hash::make($request->password),
        ]);
        if ($user) {
            $user->sendEmailVerificationNotification();
            $data = decrypt(session('provider_data'));
            $provider = @$data['provider'];
            $socialProvider = new SocialProvider();
            $socialProvider->user_id = $user->id;
            $socialProvider->$provider = $data['id'];
            $socialProvider->save();
            $this->createAdminNotify($user);
            $this->createLog($user);
            Session::forget('provider_data');
            Auth::login($user);
            return redirect()->route('user.gallery.index');
        }
    }
}