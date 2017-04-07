<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\UserLike;
use App\Notifications\LikeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mockery\Exception;
use Validator;


class LikeController extends Controller
{
    /**
     * Display a list of likes
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try
        {

            $photo_id = Input::get('photo_id');
            $page = Input::get('page', 0);
            $limit = Input::get('limit', config('app.likes_per_page'));

            $validator = Validator::make(
                    ['photo_id' => $photo_id, 'limit' => $limit, 'page'=> $page],
                    ['photo_id' => ['required', 'numeric'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $likes = UserLike::with('User')->where('photo_id', $photo_id)->offset($page*$limit)->limit($limit)->orderBy('like_id', 'desc')->get();

           return response()->json([
               'status' => TRUE,
               'likes' => $likes->isEmpty() ? [] : $likes->toArray(),
               'total' => $likes->isEmpty() ? 0 : Photo::find($photo_id)->likes_count
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
     * Store a like
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        try
        {
            $validator = Validator::make($input,
                ['photo_id' => ['required', 'numeric']]
            );

            if(!$validator->passes())
            {
                throw new Exception('invalid_param');
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $photo = Photo::find($input['photo_id']);

            if(!$photo)
            {
                throw new Exception("resource_not_found");
            }

            if(!$photo->like_enabled)
            {
                throw new Exception('invalid_action');
            }

            $like = new UserLike();
            $like->user_id = \Auth::user()->user_id;
            $like->photo_id = $photo->photo_id;
            $like->created_at = Carbon::now();

            if(!$like->save())
            {
                throw new Exception('internal_error');
            }

            $photo->likes_count++;
            /** @noinspection PhpUndefinedMethodInspection */

            $photo->save();

            if($photo->User->user_id != \Auth::user()->user_id)
            {
                $photo->User->notify(new LikeNotification($photo->User, $photo->photo_id));
            }

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
     * Dislike a photo
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
            $photo = Photo::find($id);

            if(!$photo)
            {
                throw new Exception("resource_not_found");
            }

            if($photo->like_enabled)
            {
                throw new Exception('invalid_action');
            }

            /** @noinspection PhpUndefinedMethodInspection */
            if(!$like = UserLike::where(['user_id' => \Auth::user()->user_id, 'photo_id' => $photo->photo_id]))
            {
                throw new Exception('internal_error');
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $like->delete();

            $photo->likes_count--;
            /** @noinspection PhpUndefinedMethodInspection */
            $photo->save();

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
}