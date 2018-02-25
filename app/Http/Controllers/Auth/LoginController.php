<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login(Request $request)
    {
        $this->validate(request(), [
            'username' => 'required|min:3|max:255',
            'password' => 'required',
        ]);

        $credentials = request()->only('username', 'password');

        if (Auth::attempt($credentials, request()->has('remember') ) ) {
            $user = Auth::user();
            // Allow only if user is root or enabled.
            if ($user->enabled) {
                $user->generateToken();
            }
        }

    }


    public function logout(Request $request) {
        $user = Auth::user();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        Auth::logout();

        return redirect(route('login') );
    }
}
