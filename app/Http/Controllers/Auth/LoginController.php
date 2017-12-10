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
    /*protected $redirectTo = '/test';*/

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        /*if (auth()->check() && auth()->user()->isAdmin()) {
            return 'Admin View.';
        }*/

        return redirect()->intended('/');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        /*login old users*/
        $this->loginOldUser($request);
        /*login old users*/

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function loginOldUser($request)
    {
        $user = \App\User::where($this->username(), $request->{$this->username()})->where('password', '')->where('password_old', md5($request->password))->first();
        if($user)
        {
            $user->password = bcrypt($request->{$this->username()});
            $user->save();
            \Auth::login($user);
            return redirect()->intended('/');
        }
    }

    public function username()
    {

        if(filter_var(request()->email, FILTER_VALIDATE_EMAIL))
        {
            return 'email';
        }
        if(!isset( request()->username))
        {
            $request = request()->all();
            $request['username'] = $request['email'];
            unset($request['email']);
            request()->replace($request);  
        }
        return 'username';
    }
}
