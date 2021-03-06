<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/';

    // protected function authenticated(Request $request, $user)
    // {
    //     return redirect($this->redirectTo());
    // }

    public function username()
    {
        return 'name';
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->force_logout = false;
        $user->save();
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $this->guard()->logout();

        $request->session()->invalidate();
        $user->checkertasks()->update(['isworking' => false]);

        return $this->loggedOut($request) ?: redirect('/');
    }
}
