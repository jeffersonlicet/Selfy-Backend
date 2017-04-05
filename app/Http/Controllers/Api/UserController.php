<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Validator;

class UserController extends Controller
{
    /**
     * Return a user information
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try
        {
            $validator =
                Validator::make(
                    ['id' => $id],
                    ['id' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'likes' => $validator->messages()->toArray()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            if ($result = User::find($id))
            {
                return response()->json([
                    'status' => TRUE,
                    'user' => $result->toArray()
                ]);
            }

            throw new Exception('resource_not_found');
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $validator =
                Validator::make(
                    ['id' => $id],
                    ['id' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->toArray()
                ]);
            }

            if($id == \Auth::user()->user_id)
            {
                $values = $request->only(['bio', 'firstname' , 'lastname']);
                $validator = Validator::make(
                        $values,
                        [
                            'firstname'				=>	'required|string',
                            'lastname'				=>	'required|string',
                            'bio'				=>	'string',
                        ]
                    );

                if(!$validator->passes())
                {
                    return response()->json([
                        'status' => TRUE,
                        'report' => $validator->messages()->toArray()
                    ]);
                }

                \Auth::user()->update($values);
                \Auth::user()->touch();
                \Auth::user()->save();

                return response()->json([
                    'status' => TRUE,
                    'report' => 'resource_updated'
                ]);

            }

            throw new Exception("invalid_request");
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }
}
