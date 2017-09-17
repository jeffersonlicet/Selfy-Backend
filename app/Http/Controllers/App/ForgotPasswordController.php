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
}
