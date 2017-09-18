<?php

namespace App\Http\Controllers\Api;

use App;
use Hash;
use Illuminate\Support\Facades\Password;
use Mail;
use JWTAuth;
use Exception;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserKey;
use Illuminate\Http\Request;
use App\Models\UserInformation;
use App\Http\Controllers\Controller;
use App\Mail\FbIntegrationConfirmMail;

class AuthController extends Controller
{
    /**
     * Create an user account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try
        {
            $input = $request->all();
            $validator = Validator::make($input, User::getCreateRules());

            if ($validator->passes())
            {
                $user = new User();
                $user->username = $input['username'];

                if ($request->has('email'))
                    $user->email = $input['email'];

                if ($request->has('firstname'))
                    $user->firstname = $input['firstname'];

                if ($request->has('lastname'))
                    $user->lastname = $input['lastname'];

                if ($request->has('firebase_token'))
                    $user->firebase_token = $input['firebase_token'];

                if ($request->has('wp_token'))
                    $user->wp_token = $input['wp_token'];

                if ($request->has('original_platform'))
                    $user->original_platform = $input['original_platform'];

                $user->password = Hash::make($input['password']);
                $user->user_locale = App::getLocale();

                $user->avatar = 'http://i.imgur.com/Q42Sl3B.jpg';

                if ($user->save())
                {
                    $public = JWTAuth::fromUser($user);
                    $private = str_random(50);

                    $token = new App\Models\UserToken();
                    $token->public_key = $public;
                    $token->private_key = $private;
                    $token->user_id = $user->user_id;

                    if ($request->has('device_os')){}
                        $token->device_os = $input['device_os'];

                    if ($request->has('device_id'))
                        $token->device_id = $input['device_id'];

                    $token->save();

                    return response()->json([
                        'status' => TRUE,
                        'public_key' => $public,
                        'private_key' => $private,
                        'user' => $user->toArray()
                    ]);
                }
            }

            return response()->json([
                'status' => FALSE,
                'report' => $validator->messages()->first()
            ]);
        }

        catch(\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Login a user using credentials
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'username' => 'required',
                'password' => 'required',
            ]);

            if ($validator->passes())
            {
                $sanitized = filter_var($input['username'], FILTER_SANITIZE_EMAIL);

                if($user = User::withTrashed()
                    ->with('Face')
                    ->where(($sanitized == $input['username'] && filter_var($sanitized, FILTER_VALIDATE_EMAIL)) ? 'email' : 'username', $input['username'])->first())
                {
                    //TODO change by constant 0 = normal password
                    if($user->password_type ==  1)
                    {
                        $checkPassword = Hash::check(Hash::make($input['password']), $user->password);
                    } else
                    {
                        $checkPassword = Hash::check($input['password'], $user->password);
                    }

                    if ($checkPassword)
                    {
                        $user->user_locale = App::getLocale();
                        $user->save();

                        if($user->Token != null)
                            $user->Token->delete();

                        if ($request->has('firebase_token'))
                        {
                            $user->firebase_token = $input['firebase_token'];
                            $user->wp_token = null;
                            $user->save();
                        }

                        $public = JWTAuth::fromUser($user);
                        $private = str_random(50);

                        $token = new App\Models\UserToken();
                        $token->public_key = $public;
                        $token->private_key = $private;
                        $token->user_id = $user->user_id;

                        if ($request->has('device_os'))
                            $token->device_os = $input['device_os'];

                        if ($request->has('device_id'))
                            $token->device_id = $input['device_id'];

                        $token->save();

                        unset($user->token);

                        return response()->json([
                            'status' => TRUE,
                            'public_key' => $public,
                            'private_key' => $private,
                            'user' => $user->toArray()
                        ]);

                    } else {
                        return response()->json([
                            'status' => FALSE,
                            'report' => "Invalid password"
                        ]);
                    }
                }

                return response()->json([
                    'status' => FALSE,
                    'report' => 'User not found'
                ]);
            }

            return response()->json([
                'status' => FALSE,
                'report' => $validator->messages()->first()
            ]);

        }
        catch(\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     *  Refresh a public token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public  function refresh(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'private_key' => 'required'
            ]);

            if ($validator->passes())
            {
                $token = App\Models\UserToken::where('private_key', $input['private_key'])->firstOrFail();
                $user = $token->User;
                $user->token->delete();

                $public = JWTAuth::fromUser($user);
                $private = str_random(50);

                $token = new App\Models\UserToken();
                $token->public_key = $public;
                $token->private_key = $private;
                $token->user_id = $user->user_id;

                if ($request->has('device_os'))
                    $token->device_os = $input['device_os'];

                if ($request->has('device_id'))
                    $token->device_id = $input['device_id'];

                $token->save();

                return response()->json([
                    'status' => TRUE,
                    'public_key' => $public,
                    'private_key' => $private,
                ]);
            }

            return response()->json([
                'status' => FALSE,
                'report' => $validator->messages()->first()
            ]);

        }
        catch(\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Link a Facebook account with an existing user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function link_facebook(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'email' => 'required|email',
            ]);

            if (!$validator->passes())
                return response()->json(['status'=>FALSE, 'report'=>$validator->messages()->first()]);

            if(!$user = User::where('email', $input['email'])->first())
                return response()->json(['status'=>FALSE, 'report'=> "invalid_action"]);

            if ($request->has('gender') && $user->gender == 0)
            {
                if($request->has('gender') == 'm')
                    $user->gender = 1;
                elseif ($request->has('gender') == 'f')
                    $user->gender = 2;
            }

            $user->facebook = config('constants.SOCIAL_STATUS.COMPLETED');
            $user->save();

            $public = JWTAuth::fromUser($user);
            $private = str_random(50);

            $token = new App\Models\UserToken();
            $token->public_key = $public;
            $token->private_key = $private;
            $token->user_id = $user->user_id;

            if ($request->has('device_os'))
                $token->device_os = $input['device_os'];

            if ($request->has('device_id'))
                $token->device_id = $input['device_id'];

            $token->save();

            $userInfo = new UserInformation();
            $userInfo->facebook_id = $input['fb_id'];
            $userInfo->user_id = $user->user_id;
            $userInfo->facebook_email = $input['email'];
            $userInfo->save();

            return response()->json([
                'status' => TRUE,
                'public_key' => $public,
                'private_key' => $private,
                'user' => $user->toArray()
            ]);

        }

        catch(Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Determine if an account has facebook associated
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync_facebook(Request $request)
    {
        try
        {
            $input = $request->all();
            $validator = Validator::make($input, ['identity' => 'required|email']);
            $public = '';
            $private = '';

            if (!$validator->passes())
                return response()->json(['status' => FALSE, 'report' => $validator->messages()->first()]);

            $alternate = UserInformation::has('User')->with('User')->where('facebook_email', $input['identity'])->first();
            if(($user = User::with('information')->where('email', $input['identity'])->first()) || $alternate)
            {
                if($alternate) $user = $alternate->User;
                $report = 'confirmation_required';

                if($user->facebook == config('constants.SOCIAL_STATUS.COMPLETED'))
                {
                    $report = 'exists';
                    $user->token->delete();

                    if ($request->has('firebase_token'))
                    {
                        $user->firebase_token = $input['firebase_token'];
                        $user->save();
                    }

                    $public = JWTAuth::fromUser($user);
                    $private = str_random(50);

                    $token = new App\Models\UserToken();
                    $token->public_key = $public;
                    $token->private_key = $private;
                    $token->user_id = $user->user_id;

                    if ($request->has('device_os'))
                        $token->device_os = $input['device_os'];

                    if ($request->has('device_id'))
                        $token->device_id = $input['device_id'];

                    $token->save();
                    unset($user->token);
                }

                elseif($user->facebook == config('constants.SOCIAL_STATUS.PENDING'))
                {
                    if($old_key = App\Models\UserKey::where(['key_type' => config('constants.KEY_TYPE.FACEBOOK_INTEGRATION_CONFIRM'),
                        'user_id' => $user->user_id,
                       ])->where('updated_at', '<', Carbon::today())->first())
                    {
                        $old_key->delete();

                        $key = new App\Models\UserKey();
                        $key->user_id = $user->user_id;
                        $key->key_type = config('constants.KEY_TYPE.FACEBOOK_INTEGRATION_CONFIRM');
                        $key->key_value = str_random(15);
                        $key->save();
                        $user->facebook = config('constants.SOCIAL_STATUS.PENDING');
                        $user->save();

                        Mail::to($user)->send(new FbIntegrationConfirmMail($user, $key->key_value));
                        $report = 'confirmation_required';
                    }
                    else $report = 'email_sent';
                }

                elseif($user->facebook == config('constants.SOCIAL_STATUS.CONFIRMED')) $report = 'email_confirmed';
                elseif($user->facebook == config('constants.SOCIAL_STATUS.IMPLICIT')) $report = 'is_implicit';
                else
                {
                    $keys = App\Models\UserKey::where(['key_type' => config('constants.KEY_TYPE.FACEBOOK_INTEGRATION_CONFIRM'),
                        'user_id' => $user->user_id])->get();

                    foreach($keys as $k)
                    {
                        $k->delete();
                    }

                    $key = new App\Models\UserKey();
                    $key->user_id = $user->user_id;
                    $key->key_type = config('constants.KEY_TYPE.FACEBOOK_INTEGRATION_CONFIRM');
                    $key->key_value = str_random(15);
                    $key->save();
                    $user->facebook = config('constants.SOCIAL_STATUS.PENDING');
                    $user->save();

                    Mail::to($user)->send(new FbIntegrationConfirmMail($user, $key->key_value));
                }
            }
            else $report = 'register_required';

            return response()->json(['status' => TRUE, 'report'=> $report, 'user' => $user,
                'public_key' => $public,
                'private_key' => $private]);
        }

        catch(Exception $e)
        {
            return response()->json(['status' => FALSE, 'report' => $e->getMessage()]);
        }
    }

    /**
     * Determine if an account has facebook associated without
     * altering tokens
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync_facebook_implicit(Request $request)
    {
        try
        {
            $input = $request->all();
            $validator = Validator::make($input, ['identity' => 'required|email', 'facebook_id' => 'required']);

            if (!$validator->passes())
                return response()->json(['status' => FALSE, 'report' => $validator->messages()->first()]);

            switch (\Auth::user()->facebook)
            {
                /**
                 *  SOCIAL STATUS IMPLICIT: POSSIBLE CASES
                 *
                 * #1 There was no Facebook account existing with this email/facebook_id, so we create the information
                 * data and the user will only login using his Selfy credentials.
                 */
                case config('constants.SOCIAL_STATUS.IMPLICIT'):
                /**
                 *  SOCIAL STATUS COMPLETED: POSSIBLE CASES
                 *
                 * #1 User created his account using Facebook.
                 * #2 The user requested Facebook integration from the login page and then
                 * requested any Facebook action inside the app so we forced the integration.
                 */
                case config('constants.SOCIAL_STATUS.COMPLETED'):
                    if(\Auth::user()->information->facebook_id == $input['facebook_id'])

                        return response()->json([
                            'status' => TRUE,
                            'report'=> 'exists',
                            'user' => \Auth::user()->toArray()
                        ]);

                    break;

                /**
                 *  SOCIAL STATUS PENDING: POSSIBLE CASES
                 *
                 * #1 This email account may be the same that the provided facebook email, because we ask the user
                 * to confirm in an attempt to login using Facebook. But the users logged in the app using Selfy
                 * credentials, so now we ?are sure? that is the same user, lets force integration
                 *
                 */
                case config('constants.SOCIAL_STATUS.PENDING'):
                /**
                 *  SOCIAL STATUS CONFIRMED: POSSIBLE CASES
                 *
                 * #1 This email account may be the same that the provided facebook email, because we ask the user
                 * to confirm in an attempt to login using Facebook. And the user confirmed the email but there was no
                 * Facebook actions, lets force integration .
                 *
                 */
                case config('constants.SOCIAL_STATUS.CONFIRMED'):

                    if(\Auth::user()->email == $input['identity'])
                    {
                        \Auth::user()->facebook = config('constants.SOCIAL_STATUS.COMPLETED');
                        \Auth::user()->touch();
                        \Auth::user()->save();

                        $info = new UserInformation();
                        $info->facebook_id = $input['facebook_id'];
                        $info->user_id = \Auth::user()->user_id;
                        $info->facebook_email = $input['identity'];
                        $info->save();

                        //Clean confirm integration keys
                        $keys = UserKey::where([
                            'key_type' => config('constants.KEY_TYPE.FACEBOOK_INTEGRATION_CONFIRM'),
                            'user_id' => \Auth::user()->user_id
                        ])->get();

                        foreach($keys as $k) $k->delete();

                        return response()->json([
                            'status' => TRUE,
                            'report'=> 'exists',
                            'user' => \Auth::user()->toArray()
                        ]);
                    }

                   /**
                     * Integration error
                    *
                     * This facebook account is not the same one used to ask login integration
                     */
                    else
                    {
                        return response()->json([
                            'status' => TRUE,
                            'report'=> 'invalid_action',
                        ]);
                    }

                    break;

                /**
                 *  SOCIAL STATUS UNSET: POSSIBLE CASES
                 *
                 * #1 The user does not have any facebook interaction request, first let's check if other user exist
                 * with this email, then check facebook_id, if everything is clean, let's associate the accounts in a
                 * implicit way, the user can only login using Selfy credentials.
                 *
                 */
                case config('constants.SOCIAL_STATUS.UNSET'):

                    if(\Auth::user()->email == $input['identity'] || (!User::where('email', $input['identity'])->first() && !UserInformation::where('facebook_id', $input['facebook_id'])->first()))
                    {
                       /* Let's create the link */
                        \Auth::user()->facebook = config('constants.SOCIAL_STATUS.IMPLICIT');
                        \Auth::user()->touch();
                        \Auth::user()->save();

                        $info = new UserInformation();
                        $info->facebook_id = $input['facebook_id'];
                        $info->user_id = \Auth::user()->user_id;
                        $info->facebook_email = $input['identity'];
                        $info->save();

                        return response()->json([
                            'status' => TRUE,
                            'report'=> 'exists',
                            'user' => \Auth::user()->toArray()
                        ]);
                    }


                    /**
                     * Integration error
                     *
                     * This facebook account is linked with other Selfy account
                     */
                    else
                    {
                        return response()->json(['status' => TRUE,
                            'report'=> 'invalid_action',
                        ]);
                    }

                    break;
            }

            return response()->json([
                'status' => TRUE,
                'report'=> 'invalid_action',
            ]);
        }

        catch(Exception $e)
        {
            return response()->json(['status' => FALSE, 'report' => $e->getMessage()]);
        }
    }

    /**
     * Create an user account using facebook data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_facebook(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'email' => 'required|email',
                'fb_id' => 'required'
            ]);

            if (!$validator->passes())
                return response()->json(['status'=>FALSE, 'report'=>$validator->messages()->first()]);

            if($user = User::where('email', $input['email'])->first())
                return response()->json(['status' =>FALSE, 'report' => 'please try again']);

            $user = new User();

            if ($request->has('gender'))
            {
                if($request->has('gender') == 'm')
                    $user->gender = 1;
                elseif ($request->has('gender') == 'f')
                    $user->gender = 2;
            }

            $user->facebook = config('constants.SOCIAL_STATUS.COMPLETED');

            if ($request->has('firstname'))
                $user->firstname = $input['firstname'];

            if ($request->has('bio'))
                $user->bio = $input['bio'];

            if ($request->has('avatar'))
                $user->avatar = $input['avatar'];
            else $user->avatar = 'http://i.imgur.com/4sfeHin.jpg';

            if ($request->has('lastname'))
                $user->lastname = $input['lastname'];

            if ($request->has('firebase_token'))
                $user->firebase_token = $input['firebase_token'];

            $user->user_locale = App::getLocale();

            if ($user->save())
            {
                $public = JWTAuth::fromUser($user);
                $private = str_random(50);

                $token = new App\Models\UserToken();
                $token->public_key = $public;
                $token->private_key = $private;
                $token->user_id = $user->user_id;

                if ($request->has('device_os'))
                    $token->device_os = $input['device_os'];

                if ($request->has('device_id'))
                    $token->device_id = $input['device_id'];

                $token->save();

                $userInfo = new UserInformation();
                $userInfo->facebook_id = $input['fb_id'];
                $userInfo->user_id = $user->user_id;
                $userInfo->facebook_email = $input['email'];
                $userInfo->save();

                return response()->json([
                    'status' => TRUE,
                    'public_key' => $public,
                    'private_key' => $private,
                    'user' => $user->toArray()
                ]);
            }
            return response()->json([
                'status' => FALSE,
                'report' => $validator->messages()->first()
            ]);
        }

        catch(Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Action to perform after click link from mobile to
     * confirm facebook integration
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm_facebook(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'code' => 'required',
            ]);

            if (!$validator->passes())
                return response()->json(['status'=> FALSE, 'report' => $validator->messages()->first()]);

            if($code = UserKey::where('key_value', $input['code'])->first())
            {
                if($code->key_status == config('constants.KEY_STATUS.VALID') && $code->updated_at >= Carbon::today())
                {
                    $code->key_status = config('constants.KEY_STATUS.EXPIRED');
                    $code->User->facebook = config('constants.SOCIAL_STATUS.CONFIRMED');
                    $code->User->save();
                    $code->delete();

                    return response()->json(['status'=> TRUE, 'report' => 'action_done']);
                }
                else
                {
                    $code->key_value = str_random(15);
                    $code->touch();
                    $code->save();

                    Mail::to($code->User)->send(new FbIntegrationConfirmMail($code->User, $code->key_value));
                    return response()->json(['status'=> FALSE, 'report' => 'action_resend']);
                }
            }

            return response()->json([
                'status' => FALSE,
                'report' => 'invalid_key'
            ]);
        }

        catch(Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $input = $request->only(['email']);

            $validator = Validator::make($input, [
                'email' => 'required',
            ]);

            if (!$validator->passes())
                return response()->json(['status'=> false, 'report' => $validator->messages()->first()]);

            if($user = User::where('email', $input['email'])->first())
            {
                $response = Password::broker()->sendResetLink($input);

                if($response == Password::RESET_LINK_SENT)
                {
                    return response()->json(['status'=> true, 'report' => "Email sent"]);
                }
            }

            return response()->json(['status'=> false, 'report' => "Email not found"]);
        }

        catch(Exception $e)
        {
            return response()->json([
                'status' => false,
                'report' => $e->getMessage()
            ]);
        }
    }
}
