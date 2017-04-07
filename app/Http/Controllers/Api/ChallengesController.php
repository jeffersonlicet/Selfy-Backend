<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeCompleted;
use App\Models\ChallengeTodo;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Validator;

class ChallengesController extends Controller
{

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
                    'likes' => $validator->messages()->first()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            if ($result = Challenge::with('Object')->find($id))
            {
                return response()->json([
                    'status' => TRUE,
                    'challenge' => $result->toArray()
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
     *  Add challenge to: to do list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'challenge_id' => ['required', 'numeric']
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if(!$challenge = Challenge::find($input['challenge_id']))
            {
                throw new Exception("resource_not_found");
            }

            if(ChallengeTodo::where(['user_id' => \Auth::user()->user_id, 'challenge_id' => $input['challenge_id']])->first())
            {
                throw new Exception('invalid_action');
            }

            $todo = new ChallengeTodo();
            $todo->user_id = \Auth::user()->user_id;
            $todo->challenge_id = $input['challenge_id'];
            $todo->created_at = Carbon::now();
            $todo->saveOrFail();

            $increment = $challenge->object_type."_todo";
            \Auth::user()->{$increment}++;
            \Auth::user()->save();

            return response()->json([
                'status' => TRUE,
                'report' => 'action_done'
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
     * Delete a to do challenge from my list
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try
        {
            $validator = Validator::make(
                ['id' => $id],
                ['id' => ['required', 'numeric']]
            );

            if(!$validator->passes())
            {
                throw new Exception('invalid_param');
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $todo = ChallengeTodo::where('challenge_id', $id)->first();

            if(!$todo)
            {
                throw new Exception("resource_not_found");
            }

            if($todo->user_id != \Auth::user()->user_id)
            {
                throw new Exception('invalid_action');
            }

            \Auth::user()->{$todo->Challenge->object_type."_todo"}--;
            \Auth::user()->save();
            $todo->delete();

            return response()->json([
                'status' => true,
                'report' => 'action_done'
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
     * Get To Do challenges
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function todo()
    {
        try
        {

            $user_id = Input::get('user_id', \Auth::user()->user_id);
            $page = Input::get('page', 0);
            $limit = Input::get('limit', config('app.likes_per_page'));

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

            /** @noinspection PhpUndefinedMethodInspection */
            $challenges = ChallengeTodo::where('user_id',$user_id)->offset($page*$limit)->limit($limit)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => TRUE,
                'challenges' => $challenges->isEmpty() ? [] : $challenges->toArray()
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
     * Get completed challenges
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function completed()
    {
        try
        {

            $user_id = Input::get('user_id', \Auth::user()->user_id);
            $page = Input::get('page', 0);
            $limit = Input::get('limit', config('app.likes_per_page'));

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

            /** @noinspection PhpUndefinedMethodInspection */
            $challenges = ChallengeCompleted::where('user_id',$user_id)->offset($page*$limit)->limit($limit)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => TRUE,
                'challenges' => $challenges->isEmpty() ? [] : $challenges->toArray()
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
