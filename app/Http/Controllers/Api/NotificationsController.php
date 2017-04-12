<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\User;
use App\Models\UserComment;
use Illuminate\Support\Facades\Input;
use Validator;

class NotificationsController extends Controller
{
    public function index()
    {
        try
        {
            $page = Input::get('page', 0);
            $limit = Input::get('limit', config('app.notifications_per_page'));

            $validator =
                Validator::make(
                    ['limit' => $limit, 'page'=> $page],
                    ['limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $curated = [];

            \Auth::user()->unreadNotifications->markAsRead();

            /** @noinspection PhpUndefinedMethodInspection */
            foreach (\Auth::user()->notifications()->offset($page*$limit)->limit($limit)->get() as $n)
            {
                $notification = [];
                $notification['notification_id'] = $n->id;
                $notification['read'] = $n->read_at != null;
                $notification['type'] = explode("\\", $n->type)[2];
                $notification['created_at'] = $n->created_at->toDateTimeString();
                switch ($n->type)
                {
                    case  'App\Notifications\DuoNotification':
                        if($photo = Photo::with('Challenges', 'Challenges.Object')->find($n->data['photo_id']))
                        {
                            $notification['photo'] = $photo->toArray();
                        }
                        else
                        {
                            continue;
                        }
                    break;

                    case 'App\Notifications\SpotNotification':
                    if($photo = Photo::with('Place')->find($n->data['photo_id']))
                    {
                        $notification['photo'] = $photo->toArray();
                    }
                    else
                    {
                        continue;
                    }
                    break;

                    case 'App\Notifications\LikeNotification':
                        if($photo = Photo::find($n->data['photo_id']))
                        {
                            $notification['photo'] = $photo->toArray();
                        }
                        else
                        {
                            continue;
                        }

                        if($user = User::find($n->data['user_id']))
                        {
                            $notification['user'] = $user->toArray();
                        }
                        else
                        {
                            continue;
                        }
                        break;

                    case 'App\Notifications\FollowNotification':
                        if($user = User::find($n->data['user_id']))
                        {
                            $notification['user'] = $user->toArray();
                        }
                        else
                        {
                            continue;
                        }

                    break;

                    case 'App\Notifications\CommentNotification':
                        if($photo = Photo::find($n->data['photo_id']))
                        {
                            $notification['photo'] = $photo->toArray();
                        }
                        else
                        {
                            continue;
                        }

                        if($comment = UserComment::find($n->data['comment_id']))
                        {
                            $comment = $comment->toArray();
                            unset($comment['photo']);

                            $notification['comment'] = $comment;
                        }
                        else
                        {
                            continue;
                        }

                        if($user = User::find($n->data['user_id']))
                        {
                            $notification['user'] = $user->toArray();
                        }
                        else
                        {
                            continue;
                        }
                        break;
                }

                $curated[] = $notification;

            }

            return response()->json([
                'status' => TRUE,
                'notifications' => $curated
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
