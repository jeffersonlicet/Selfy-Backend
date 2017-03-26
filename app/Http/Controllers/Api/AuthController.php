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
    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, User::getCreateRules());

        if ($validator->passes())
        {
            $user = new User();

            $user->email        = $input['email'];
            $user->username     = $input['username'];
            $user->firstname    = $input['firstname'];
            $user->lastname     = $input['lastname'];

            if($request->has('firebase_token'))
                $user->firebase_token     = $input['firebase_token'];

            $user->password = Hash::make($input['password']);
            $user->user_locale = App::getLocale();

            if(TRUE)
            {
                $private = JWTAuth::fromUser($user);
                $public  = JWTAuth::fromUser($user);

                return response()->json([
                    'status' => TRUE,
                    'public_key'  =>  $public,
                    'private_key' =>  $private
                ]);
            }
        }
        else
        {
            return response()->json([
                'status' => FALSE,
                'report' =>  $validator->messages()->toArray()
            ]);
        }
    }

    public function login(Request $request)
    {

    }
}
