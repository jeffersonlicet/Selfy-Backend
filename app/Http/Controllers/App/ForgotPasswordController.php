<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Mail\FbIntegrationConfirmMail;
use App\Models\UserKey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mail;
use Validator;

class ForgotPasswordController extends Controller
{
    /**
     * @return string
     */
    public function showResetForm()
    {
        return view('backend.reset_password')->with(['pageTitle'=> 'Selfy'])->render();
    }
}
