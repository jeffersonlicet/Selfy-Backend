<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

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

    protected function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        if(!\Auth::guest())
        {
            \Auth::user()->password_type = config('constants.APP_PLATFORMS.android');
            \Auth::user()->save();
        }

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
    }
}
