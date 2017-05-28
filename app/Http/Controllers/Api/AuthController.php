<?php

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Mail\FbIntegrationConfirmMail;

use App\Models\User;
use App\Models\UserInformation;
use App\Models\UserKey;
use Carbon\Carbon;
use Exception;
use Hash;
use JWTAuth;
use Mail;
use Validator;
use Illuminate\Http\Request;

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

                    if ($request->has('device_os'))
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
                if($user = User::withTrashed()->with('Face')->where('username', $input['username'])->first())
                {
                    if (Hash::check($input['password'], $user->password))
                    {
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
}
