<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['activo'] = 1;
        if (Auth::attempt($credentials, true)) {
            $user = Auth::user();
            $user->update([
                'api_token' => Str::random(60)
            ]);
            return response()->json([[
                'success' => 'success',
                'api_token' => $user->api_token,
                'role'=> $user->rol],
                200]);
        } else {
            return response()->json(['error' => 'Forbidden'],403);
        }
    }

    public function logout()
    {
        $user = Auth::guard('api')->user();
        if($user){
            $user->update([
                'api_token' => null
            ]);
            return response()->json(['success' => 'success'],200);
        }
    }
}
