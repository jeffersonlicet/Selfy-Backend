<?php

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Models\User;

use Hash;
use JWTAuth;
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

                $user->email = $input['email'];
                $user->username = $input['username'];

                if ($request->has('firstname'))
                    $user->firstname = $input['firstname'];

                if ($request->has('lastname'))
                $user->lastname = $input['lastname'];

                if ($request->has('firebase_token'))
                    $user->firebase_token = $input['firebase_token'];

                $user->password = Hash::make($input['password']);
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
                    if (Hash::check($input['password'], $user->password)) {
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
}