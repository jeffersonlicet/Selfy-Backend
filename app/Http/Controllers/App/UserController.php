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

class UserController extends Controller
{
    public function confirm_facebook_link()
    {
        $code = Input::get('code', null);

        if($code = UserKey::where('key_value', $code)->first())
        {
            if($code->key_status == config('constants.KEY_STATUS.VALID') && $code->updated_at >= Carbon::today())
            {
                $code->key_status = config('constants.KEY_STATUS.EXPIRED');
                $code->User->facebook = config('constants.SOCIAL_STATUS.CONFIRMED');
                $code->User->save();
                $code->delete();

                return view('pages.message')->with(['pageTitle'=> 'Selfy', 'messageTitle' => 'Nice!',
                    'messageBody' => 'Now you will be able to use your Facebook account with your Selfy account.'])->render();
            }
            else
            {
                $code->key_value = str_random(15);
                $code->touch();
                $code->save();

                Mail::to($code->User)->send(new FbIntegrationConfirmMail($code->User, $code->key_value));
                return view('pages.message')->with(['pageTitle'=> 'Selfy', 'messageTitle' => 'Oops!',
                    'messageBody' => 'The token has expired please try again. We\'ve sent your another mail.'])->render();
            }
        }

        else
            return view('pages.message')->with(['pageTitle'=> 'Selfy', 'messageTitle' => 'Oops!',
                'messageBody' => 'The token has expired or is invalid.'])->render();
    }

    public function contact(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email'=> 'required|email',
            'message' =>'required'
        ]);

        if(!$validator->passes())
            return response()->json(['status'=> FALSE, 'report' => $validator->messages()->first()]);

        Mail::send(new ContactMail($input['name'], $input['email'], $input['message']));

        return response()->json(['status'=> TRUE]);
    }
}
