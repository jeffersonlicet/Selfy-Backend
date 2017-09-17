<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Mail\FbIntegrationConfirmMail;
use App\Models\UserKey;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mail;
use Validator;

class ForgotPasswordController extends Controller
{
    protected $redirectTo = '/user/password_changed';
    use ResetsPasswords;

    /**
     * Show reset form
     *
     * @param $token
     * @return string
     */
    public function showResetForm($token)
    {
        return view('pages.reset_password')->with([
            'pageTitle'=> __('app.selfy_support'), 'token' => $token, 'metaTags' => null])->render();
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        $this->guard()->login($user);
        $user->password_type = config('constants.APP_PLATFORMS.android');
        $user->save();
    }
}
