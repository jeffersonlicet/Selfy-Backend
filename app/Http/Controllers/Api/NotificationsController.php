<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Photo;
use App\Models\User;
use App\Models\UserChallenge;
use App\Models\UserComment;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\Input;
use Musonza\Chat\Messages\Message;
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


            foreach (\Auth::user()->notifications()->offset($page*$limit)->limit($limit)->get() as $n)
            {
                $notification = [];
                $notification['notification_id'] = $n->id;
                $notification['read'] = $n->read_at != null;
                $notification['type'] = explode("\\", $n->type)[2] == 'Notifications' ?  explode("\\", $n->type)[3] : explode("\\", $n->type)[2] ;
                $notification['created_at'] = $n->created_at->toDateTimeString();

                switch ($n->type)
                {

                    case 'Musonza\Chat\Notifications\MessageSent':
                        $message = Message::with('sender')->find($n->data['message_id']);
                        if($message && $message->sender->user_id != \Auth::user()->user_id)
                        {
                            $notification['user'] = $message->sender;
                            $notification['body'] = __('notifications.MessageSent');
                        } else continue 2;

                        break;
                    case 'App\Notifications\PlayNotification':
                        if($photo = Photo::with('Challenges', 'Challenges.object')->find($n->data['photo_id']))
                        {
                            $notification['photo'] = $photo->toArray();
                        }

                        else continue 2;

                        break;
                    case  'App\Notifications\AcceptedInvitationNotification':
                        $user = User::find($n->data['user_id']);

                        if($user != null)
                        {
                            $notification['user']   = $user->toArray();
                            $notification['body'] = __('notifications.AcceptedInvitationNotification');
                        }

                        else continue 2;

                        break;

                    case  'App\Notifications\FollowInvitationNotification':
                        $invitation = UserInvitation::has('creator')->with('Creator')->where(['profile_id' => \Auth::user()->user_id, 'user_id' => $n->data['user_id']])->first();

                        if($invitation != null)
                        {
                            $notification['follow_invitation']   = $invitation->toArray();

                            if($invitation->invitation_status == config('constants.INVITATION_STATUS.ACCEPTED')){
                                $notification['body'] = __('notifications.FollowInvitationAccepted');
                            }

                            elseif($invitation->invitation_status == config('constants.INVITATION_STATUS.DECLINED')){
                                $notification['body'] = __('notifications.FollowInvitationDeclined');
                            }

                            else {
                                $notification['body'] = __('notifications.FollowInvitationNotification');
                            }
                        }

                        else continue 2;

                        break;

                    case  'App\Notifications\PhotoRevisionNotification':
                        if($photo = Photo::with('Challenges', 'Challenges.object')->find($n->data['photo_id']))
                        {
                            $notification['photo'] = $photo->toArray();
                            $notification['body'] = __('notifications.PhotoRevisionNotification');
                        }

                        else continue 2;
                        break;
                    case  'App\Notifications\DuoInvitationNotification':
                        if($challenge = Challenge::with('Object')->find($n->data['challenge_id']))
                        {
                            if($challenge->Object != null && ($challenge->challenge_status == config('constants.CHALLENGE_STATUS.INVITED') || $challenge->challenge_status == config('constants.CHALLENGE_STATUS.ACCEPTED')))
                                $notification['invitation'] = $challenge->toArray();
                            else continue 2;
                        }
                        else continue 2;
                    break;

                    case  'App\Notifications\DuoNotification':

                        if($photo = Photo::with('Challenges', 'Challenges.object')->find($n->data['photo_id']))
                        {
                            $notification['photo'] = $photo->toArray();
                        }

                        else continue 2;
                    break;

                    case 'App\Notifications\SpotNotification':
                        if($photo = Photo::with('Place')->find($n->data['photo_id']))
                        {
                            $notification['photo'] = $photo->toArray();
                        }
                        else continue 2;

                    break;

                    case 'App\Notifications\LikeNotification':
                        $photo = Photo::find($n->data['photo_id']);
                        $user = User::find($n->data['user_id']);

                        if($photo != null && $user != null)
                        {
                            $notification['photo']  = $photo->toArray();
                            $notification['user']   = $user->toArray();
                            $notification['body'] = __('notifications.LikeNotification');
                        }

                        else continue 2;
                        break;

                    case 'App\Notifications\FollowNotification':

                        if($user = User::find($n->data['user_id']))
                        {
                            $notification['user'] = $user->toArray();
                            $notification['body'] = __('notifications.FollowNotification');

                        }
                        else continue 2;

                    break;

                    case 'App\Notifications\CommentNotification':

                        $photo = Photo::find($n->data['photo_id']);
                        $comment = UserComment::find($n->data['comment_id']) ;
                        $user = User::find($n->data['user_id']);

                        if($photo != null && $comment != null && $user != null)
                        {
                            $notification['photo'] = $photo->toArray();

                            $comment = $comment->toArray();
                            unset($comment['photo']);

                            $notification['comment'] = $comment;
                            $notification['user'] = $user->toArray();
                            $notification['body'] = __('notifications.CommentNotification');

                        }
                        else continue 2;
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
