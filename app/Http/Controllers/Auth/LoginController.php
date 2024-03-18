<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

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
    protected $redirectTo = RouteServiceProvider::HOME;

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
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::join('bangil_kepegawaian.ms_user as u', 'u.id', '=', 'users.id_pegawai')
            ->select('users.*')
            ->where('u.username', $request->input('username'))
            ->where('u.password', md5($request->input('password')))
            ->first();

        if (!empty($user) && $user->count() > 0) {
            Auth::login($user);
            return redirect()->route('home');
        } else {
            return Redirect::back()->withErrors([
                'username' => 'Silahkan cek username dan password'
            ]);
        }

        self::sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
