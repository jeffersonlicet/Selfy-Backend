<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 5/31/2017
 * Time: 7:52 PM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class LoginAdminController
 * @package app\Http\Controllers\Admin
 */
class LoginAdminController extends Controller
{
    use AuthenticatesUsers;

    /**
     * LoginAdminController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * @return string
     */
    public function formLogin()
    {
        return view('admin.login.form')->with(['pageTitle'=> 'Selfy', 'messageTitle'])->render();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email', 'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            return redirect()->to(config('selfy-admin.routePrefix', '/dashboard').'/'.config('selfy-admin.afterLoginRoute'));
        }
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getLogout()
    {
        Auth::logout();
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }
}