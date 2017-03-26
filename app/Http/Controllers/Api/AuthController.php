<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            //Insert user and generate token
            return response()->json([
                'status' => TRUE,
                'public_key'  =>  "",
                'private_key' =>  ""
            ]);
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
