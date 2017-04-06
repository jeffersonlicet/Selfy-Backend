<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CheckDuo;
use App\Jobs\CheckSpot;
use App\Models\Photo;
use App\Models\UserComment;
use App\Models\UserLike;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Mockery\Exception;
use Validator;


class CommentController extends Controller
{
    /**
     * Display a list of comments
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

            $validator =
                Validator::make(
                    ['photo_id' => $photo_id, 'limit' => $limit, 'page'=> $page],
                    ['photo_id' => ['required', 'numeric'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->toArray()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $comments = UserComment::with('User')->where('photo_id', $photo_id)->offset($page*$limit)->limit($limit)->orderBy('comment_id', 'desc')->get();


            return response()->json([
               'status' => TRUE,
               'comments' => $comments->isEmpty() ? [] : $comments->toArray()
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
     * Store a comment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input,
                [
                    'photo_id' => ['required', 'numeric'],
                    'body' => ['required']
                ]
            );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => FALSE,
                    'report' => $validator->messages()->toArray()
                ]);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $photo = Photo::find($input['photo_id']);

            if(!$photo)
            {
                throw new Exception("resource_not_found");
            }


            $comment = new UserComment();
            $comment->user_id = \Auth::user()->user_id;
            $comment->photo_id = $photo->photo_id;
            $comment->body = $input['body'];

            if(!$comment->save())
            {
                throw new Exception('internal_error');
            }

            $photo->comments_count++;
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
            $comment = UserComment::find($id);

            if(!$comment)
            {
                throw new Exception("resource_not_found");
            }

            if($comment->user_id != \Auth::user()->user_id)
            {
                throw new Exception('invalid_action');
            }

            $comment->Photo->likes_count--;
            /** @noinspection PhpUndefinedMethodInspection */
            $comment->Photo->save();
            /** @noinspection PhpUndefinedMethodInspection */
            $comment->delete();

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