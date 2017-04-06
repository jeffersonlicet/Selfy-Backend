<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserFollower;
use App\Models\UserFollowing;
use App\Notifications\FollowNotification;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
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
                    'report' => $validator->messages()->first()
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

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
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
                    'report' => $validator->messages()->first()
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
                        'report' => $validator->messages()->first()
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

    /**
     *
     * Soft deletes a user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        try
        {
            if (\Auth::user()->trashed())
            {
                throw new Exception('invalid_action');
            }

            \Auth::user()->delete();

            return response()->json([
                'status' => FALSE,
                'report' => 'resource_deleted'
            ]);

        }

        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Follow a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'user_id' => 'required|numeric'
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            if(\Auth::user()->user_id != $input['user_id'] && $user = User::find($input["user_id"]))
            {
                /** @noinspection PhpUndefinedMethodInspection */
                if(!UserFollower::where(['follower_id' => \Auth::user()->user_id, 'following_id' => $user->user_id])->first())
                {
                    $user->followers_count++;
                    /** @noinspection PhpUndefinedMethodInspection */
                    $user->save();

                    \Auth::user()->following_count++;
                    \Auth::user()->save();

                    $connection = new UserFollower();
                    $connection->follower_id = \Auth::user()->user_id;
                    $connection->following_id = $user->user_id;
                    $connection->save();

                    $user->notify(new FollowNotification($user));

                    return response()->json([
                        'status' => TRUE,
                        'report' => 'action_done'
                    ]);
                }
                else
                {
                    throw new Exception("invalid_action");
                }
            }

            throw new Exception("resource_not_found");
        }

        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    /**
     * Unfollow a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'user_id' => 'required|numeric'
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            if($user = User::find($input["user_id"]))
            {
                /** @noinspection PhpUndefinedMethodInspection */
                if(UserFollower::where(['follower_id' => \Auth::user()->user_id, 'following_id' => $user->user_id])->first())
                {
                    $user->followers_count--;
                    /** @noinspection PhpUndefinedMethodInspection */
                    $user->save();

                    \Auth::user()->following_count--;
                    \Auth::user()->save();

                    /** @noinspection PhpUndefinedMethodInspection */
                    $connection = UserFollower::where(['following_id' => $user->user_id, 'follower_id' => \Auth::user()->user_id])->first();
                    /** @noinspection PhpUndefinedMethodInspection */
                    $connection->delete();

                    return response()->json([
                        'status' => TRUE,
                        'report' => 'action_done'
                    ]);
                }
                else
                {
                    throw new Exception("invalid_action");
                }
            }

            throw new Exception("resource_not_found");
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    public function followers()
    {
        try
        {
            $user_id = Input::get('user_id', \Auth::user()->user_id);
            $page = Input::get('page', 0);
            $limit = Input::get('limit', config('app.connections_per_page'));

            $validator =
                Validator::make(
                    ['user_id' => $user_id, 'limit' => $limit, 'page'=> $page],
                    ['user_id' => ['required', 'numeric'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $followers = UserFollower::where('following_id', $user_id)->with('User')->offset($page*$limit)->limit($limit)->orderBy('follow_id', 'desc')->get();

            return response()->json([
                'status' => TRUE,
                'followers' => $followers->isEmpty() ? [] : $followers->toArray()
            ]);

        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => FALSE,
                'report' => $e->getMessage()
            ]);
        }
    }

    public function following()
    {
        try
        {
            $user_id = Input::get('user_id', \Auth::user()->user_id);
            $page = Input::get('page', 0);
            $limit = Input::get('limit', config('app.connections_per_page'));

            $validator =
                Validator::make(
                    ['user_id' => $user_id, 'limit' => $limit, 'page'=> $page],
                    ['user_id' => ['required', 'numeric'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $following = UserFollowing::where('follower_id', $user_id)->with('User')->offset($page*$limit)->limit($limit)->orderBy('follow_id', 'desc')->get();

            return response()->json([
                'status' => TRUE,
                'following' => $following->isEmpty() ? [] : $following->toArray()
            ]);

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