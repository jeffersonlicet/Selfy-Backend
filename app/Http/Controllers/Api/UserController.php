<?php

namespace App\Http\Controllers\Api;

use App;
use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use App\Models\UserFace;
use App\Models\UserFollower;
use App\Models\UserFollowing;
use App\Models\UserInvitation;
use App\Notifications\DuoInvitationNotification;
use App\Notifications\FollowInvitationNotification;
use App\Notifications\FollowNotification;
use Exception;
use Expression;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Validator;
class UserController extends Controller
{
    public function test()
    {
        $a = App\Models\ChallengePlay::find(4);

        return response()->json($a->toArray());
    }

    /**
     * Return a user information
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function show($data)
    {
        try
        {
            $validator =
                Validator::make(
                    ['data' => $data],
                    ['data' => ['required']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $result = is_numeric($data) ? User::with('Face')->find($data) :
                User::with('Face')->where('username', $data)->first();

            if ($result)
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
     * Return a user information with notifications count
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try
        {
            if ($result = User::with('Face')->find(\Auth::user()->user_id))
            {
                $user = $result->toArray();
                $user['unread'] = count($result->unreadNotifications);

                return response()->json([
                    'status' => TRUE,
                    'user' => $user
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try
        {
            $values = $request->only(['bio', 'firstname' , 'lastname', 'face_url', 'duo_enabled', 'spot_enabled',
                'account_private', 'save_photos', 'play_enabled']);
            $values['duo_enabled'] = $values['duo_enabled'] == "1";
            $values['spot_enabled'] = $values['spot_enabled'] == "1";
            $values['account_private'] = $values['account_private'] == "1";
            $values['save_photos'] = $values['save_photos'] == "1";

            $validator = Validator::make(
                    $values,
                    [
                        'firstname'				=>	'required|string',
                        'lastname'				=>	'required|string',
                        'duo_enabled'           =>	'required',
                        'spot_enabled'          =>	'required',
                        'play_enabled'          =>	'required',
                        'account_private'       =>	'required',
                        'save_photos' =>	'required',
                    ]
                );

            if($values['account_private'])
            {
                $followers_id = \Auth::user()->Followers->pluck('follower_id');

                foreach($followers_id as $id)
                {
                    if(!UserInvitation::where(['user_id' => $id, 'profile_id' => \Auth::user()->user_id])->first())
                    {
                        $invitation = new UserInvitation();
                        $invitation->user_id = $id;
                        $invitation->invitation_status = config('constants.INVITATION_STATUS.ACCEPTED');
                        $invitation->profile_id = \Auth::user()->user_id;
                        $invitation->save();
                    }
                }
            }

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => FALSE,
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_creation(Request $request)
    {
        try
        {
            $values = $request->only(['bio', 'firstname' , 'lastname', 'face_url', 'duo_enabled', 'spot_enabled', 'account_private', 'save_photos', 'email']);
            $values['duo_enabled'] = $values['duo_enabled'] == "1";
            $values['spot_enabled'] = $values['spot_enabled'] == "1";
            $values['account_private'] = $values['account_private'] == "1";
            $values['save_photos'] = $values['save_photos'] == "1";

            $validator = Validator::make(
                $values,
                [
                    'firstname'				=>	'required|string',
                    'lastname'				=>	'required|string',
                    'duo_enabled'           =>	'required',
                    'spot_enabled'          =>	'required',
                    'account_private'       =>	'required',
                    'save_photos' =>	'required',
                    'email' => 'required|email|unique:users,email'
                ]
            );

            if($values['account_private'])
            {
                $followers_id = \Auth::user()->Followers->pluck('follower_id');

                foreach($followers_id as $id)
                {
                    if(!UserInvitation::where(['user_id' => $id, 'profile_id' => \Auth::user()->user_id])->first())
                    {
                        $invitation = new UserInvitation();
                        $invitation->user_id = $id;
                        $invitation->invitation_status = config('constants.INVITATION_STATUS.ACCEPTED');
                        $invitation->profile_id = \Auth::user()->user_id;
                        $invitation->save();
                    }
                }
            }

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => FALSE,
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_challenges(Request $request)
    {
        try
        {
            $values = $request->only(['play_enabled', 'duo_enabled', 'spot_enabled']);
            $values['duo_enabled'] = $values['duo_enabled'] == "1";
            $values['spot_enabled'] = $values['spot_enabled'] == "1";
            $values['play_enabled'] = $values['play_enabled'] == "1";

            $validator = Validator::make(
                $values,
                [
                    'duo_enabled'           =>	'required',
                    'spot_enabled'          =>	'required',
                    'play_enabled'       =>	'required',
                ]
            );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => FALSE,
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_username(Request $request)
    {
        try
        {
            $values = $request->only(['username']);
            $values['username'] = strtolower($values['username']);

            $validator = Validator::make(
                $values, [ 'username' =>	'required|allowed_username|unique:users,username']
            );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => FALSE,
                    'report' => $validator->messages()->first()
                ]);
            }
            if(\Auth::user()->username == null)
            {
                \Auth::user()->update($values);
                \Auth::user()->touch();
                \Auth::user()->save();

                return response()->json(['status' => TRUE,'report' => 'resource_updated']);
            }

            else return response()->json(['status' => TRUE,'report' => 'invalid_action']);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_facebook_token(Request $request)
    {
        try
        {
            $values = $request->only(['facebook_token', 'facebook_id']);

            $validator = Validator::make(
                $values, ['facebook_token' => 'required', 'facebook_id' => 'required']
            );

            if (!$validator->passes()) {
                return response()->json([
                    'status' => FALSE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if(\Auth::user()->facebook != config('constants.SOCIAL_STATUS.UNSET'))
            {
                \Auth::user()->facebook_token = $values['facebook_token'];
                \Auth::user()->touch();
                \Auth::user()->save();

                return response()->json(['status' => TRUE,'report' => 'resource_updated']);
            }
            else
                return response()->json(['status' => FALSE,'report' => 'validation_required']);
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
     * Edit user face reference url
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function face(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator =
                Validator::make(
                    $input,
                    ['photo_url' => ['required', 'string']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if(\Auth::user()->Face == null)
            {
                $face = new UserFace();
                $face->user_id = \Auth::user()->user_id;
                $face->url = $input['photo_url'];
                $face->save();
            }
            else
            {
                \Auth::user()->Face->url = $input['photo_url'];
                \Auth::user()->Face->save();
            }

            $this->generateBatchDuoInvitation(\Auth::user(), 20, 0);

            return response()->json([
                'status' => TRUE,
                'report' => 'resource_updated',

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
     * Edit user avatar reference url
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function avatar(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator =
                Validator::make(
                    $input,
                    ['photo_url' => ['required', 'string']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            \Auth::user()->avatar = $input['photo_url'];
            \Auth::user()->save();

            return response()->json([
                'status' => TRUE,
                'report' => 'resource_updated'
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
     * Edit user face firebase token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function firebase(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator =
                Validator::make(
                    $input,
                    ['firebase_token' => ['required', 'string']]
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            \Auth::user()->firebase_token = $input['firebase_token'];
            \Auth::user()->save();

            return response()->json([
                'status' => TRUE,
                'report' => 'resource_updated'
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


            if(\Auth::user()->user_id != $input['user_id'] && $user = User::find($input["user_id"]))
            {

                //Check if the account is private
                if($user->account_private)
                {
                    if(!$invitation= UserInvitation::where(['user_id' => \Auth::user()->user_id, 'profile_id' => $input["user_id"]])->first())
                    {
                        $invitation = new UserInvitation();
                        $invitation->user_id = \Auth::user()->user_id;
                        $invitation->profile_id = $input["user_id"];
                        $invitation->save();

                        $user->notify(new FollowInvitationNotification(\Auth::user()));

                        return response()->json([
                            'status' => TRUE,
                            'report' => 'invitation_sent'
                        ]);
                    }
                    else
                    {
                        throw new Exception("invitation_exists");
                    }
                }
                elseif(!UserFollower::where(['follower_id' => \Auth::user()->user_id, 'following_id' => $user->user_id])->first())
                {
                    $user->followers_count++;
                    $user->save();

                    \Auth::user()->following_count++;
                    \Auth::user()->save();

                    $connection = new UserFollower();
                    $connection->follower_id = \Auth::user()->user_id;
                    $connection->following_id = $user->user_id;
                    $connection->save();

                    $user->notify(new FollowNotification(\Auth::user()));

                    $this->makeDuoInvitation($user, \Auth::user());
                    $this->makeDuoInvitation(\Auth::user(), $user);

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

            if($user = User::find($input["user_id"]))
            {

                if(UserFollower::where(['follower_id' => \Auth::user()->user_id, 'following_id' => $user->user_id])->first())
                {
                    $user->followers_count--;
                    $user->save();

                    \Auth::user()->following_count--;
                    \Auth::user()->save();

                    $connection = UserFollower::where(['following_id' => $user->user_id, 'follower_id' => \Auth::user()->user_id])->first();
                    $connection->delete();

                    if($invitation= UserInvitation::where(['user_id' => \Auth::user()->user_id, 'profile_id' => $input["user_id"]])->first())
                        $invitation->delete();

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
     * Get user followers
     * @return \Illuminate\Http\JsonResponse
     */
    public function followers()
    {
        try
        {
            $user_id = Input::get('user_id', 0) == 0 ? \Auth::user()->user_id : Input::get('user_id', 0);
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

            $followers = UserFollower::where('following_id', $user_id)->has('User')->with('User')->offset($page*$limit)->limit($limit)->orderBy('follow_id', 'desc')->get();

            $parsed = [];

            if (!$followers->isEmpty()) {

                foreach ($followers as $entity)
                {
                    $parsed[] = $entity->user;
                }

            }

            return response()->json([
                'status' => TRUE,
                'connections' => $parsed
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
     * Get user following
     * @return \Illuminate\Http\JsonResponse
     */
    public function following()
    {
        try {
            $user_id = Input::get('user_id', 0) == 0 ? \Auth::user()->user_id : Input::get('user_id', 0);
            $page = Input::get('page', 0);
            $limit = Input::get('limit', config('app.connections_per_page'));

            $validator =
                Validator::make(
                    ['user_id' => $user_id, 'limit' => $limit, 'page' => $page],
                    ['user_id' => ['required', 'numeric'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]
                );

            if (!$validator->passes()) {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $following = UserFollowing::where('follower_id', $user_id)->has('User')->with('User')->offset($page * $limit)->limit($limit)->orderBy('follow_id', 'desc')->get();
            $parsed = [];

            if (!$following->isEmpty()) {

                foreach ($following as $entity)
                {
                    $parsed[] = $entity->user;
                }

            }

            return response()->json([
                'status' => TRUE,
                'connections' => $parsed
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
     * Get user challenges
     * @return \Illuminate\Http\JsonResponse
     */
    public function challenges()
    {
        try
        {
            $limit = Input::get('limit', config('app.photos_best_per_page'));
            $page = Input::get('page', 0);
            $type = Input::get('type');
            $status = Input::get('status');

            $curated = [];

            $validator =
                Validator::make(
                    ['limit' => $limit, 'page' => $page, 'type' => $type, 'status' => $status],
                    ['limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric'],
                        'type' => ['required', Rule::in(['duo', 'spot', 'play'])],
                        'status' => ['required', Rule::in(['completed', 'todo'])],
                    ]
                );

            if (!$validator->passes()) {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $challenges = [];

            if ($status == "todo") {
                switch ($type) {
                    case 'duo':
                        $challenges = UserChallenge::where(['user_id'=> \Auth::user()->user_id, 'challenge_status'=> config('constants.CHALLENGE_STATUS.ACCEPTED')])->whereHas('Challenge', function ($query) {
                            $query->where('object_type', 'duo');
                        })->with(['Challenge', 'Challenge.object'])->limit($limit)->offset($limit * $page)->get();
                        break;

                    case 'spot':
                        $challenges = UserChallenge::where(['user_id'=> \Auth::user()->user_id, 'challenge_status'=> config('constants.CHALLENGE_STATUS.ACCEPTED')])->whereHas('Challenge', function ($query) {
                            $query->where('object_type', 'spot');
                        })->with(['Challenge', 'Challenge.object'])->limit($limit)->offset($limit * $page)->get();
                        break;

                    case 'play':
                        $challenges = UserChallenge::where(['user_id'=> \Auth::user()->user_id, 'challenge_status'=> config('constants.CHALLENGE_STATUS.ACCEPTED')])->whereHas('Challenge', function ($query) {
                            $query->where('object_type', 'play');
                        })->with(['Challenge', 'Challenge.object'])->limit($limit)->offset($limit * $page)->get();
                        break;
                }
            } else {
                switch ($type) {
                    case 'duo':
                        $challenges = UserChallenge::where(['user_id'=> \Auth::user()->user_id, 'challenge_status'=> config('constants.CHALLENGE_STATUS.COMPLETED')])->whereHas('Challenge', function ($query) {
                            $query->where('object_type', 'duo');
                        })->with(['Challenge', 'Challenge.object'])->limit($limit)->offset($limit * $page)->get();
                        break;

                    case 'spot':
                        $challenges = UserChallenge::where(['user_id'=> \Auth::user()->user_id, 'challenge_status'=> config('constants.CHALLENGE_STATUS.COMPLETED')])->whereHas('Challenge', function ($query) {
                            $query->where('object_type', 'spot');
                        })->with(['Challenge', 'Challenge.object'])->limit($limit)->offset($limit * $page)->get();
                        break;

                    case 'play':
                        $challenges = UserChallenge::where(['user_id'=> \Auth::user()->user_id, 'challenge_status'=> config('constants.CHALLENGE_STATUS.COMPLETED')])->whereHas('Challenge', function ($query) {
                            $query->where('object_type', 'play');
                        })->with(['Challenge', 'Challenge.object'])->limit($limit)->offset($limit * $page)->get();
                        break;
                }
            }
            $challenges = $challenges->isEmpty() ? [] : $challenges->toArray();

            foreach ($challenges as $challenge) 
            {
                if ($challenge['challenge'] != null)
                    $curated[] = $challenge['challenge'];
            }

            return response()->json([
                'status' => TRUE,
                'challenges' => $curated
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
     * Search users
     * @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        try
        {
            $query    = Input::get('query', 0);
            $limit   = Input::get('limit', config('app.photos_best_per_page'));
            $page    = Input::get('page', 0);

            $validator = Validator::make(
                    ['query' => $query, 'limit' => $limit, 'page'=> $page],
                    ['query' => ['required', 'string'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $result = User::where('username', 'LIKE', '%'.$query.'%')->limit($limit)->offset($limit*$page)->get();

            return response()->json([
                'status' => TRUE,
                'connections' => $result->isEmpty() ? [] : $result->toArray()
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
     * Search friends when type @user
     * @return \Illuminate\Http\JsonResponse
     */
    public function search_mention_suggestion()
    {
        try
        {
            $query    = Input::get('query', 0);
            $limit   = Input::get('limit', config('app.photos_best_per_page'));
            $page    = Input::get('page', 0);

            $validator = Validator::make(
                ['query' => $query, 'limit' => $limit, 'page'=> $page],
                ['query' => ['required', 'string'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $a = UserFollower::where('following_id', \Auth::user()->user_id)->whereHas(
                'User', function($q) use ($query){
                    $q->where('username', 'LIKE', '%'.$query.'%');
            })->with('User')->get();

            $b = UserFollowing::where('follower_id', \Auth::user()->user_id)->whereHas(
                'User', function($q) use ($query){
                    $q->where('username', 'LIKE', '%'.$query.'%');
            })->with('User')->get();

            $result = [];
            foreach ($a->merge($b) as $el)
            {
                if(!in_array($el->User, $result))
                    $result[] = $el->User;
            }

            return response()->json([
                'status' => TRUE,
                'connections' => $result
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
     * Return duo enabled users
     * @return \Illuminate\Http\JsonResponse
     */
    public function duo()
    {
        try
        {
            $limit = Input::get('limit', config('app.likes_per_page'));
            $page = Input::get('page', 0);

            $curated = [];

            $validator =
                Validator::make(
                    ['limit' => $limit, 'page' => $page],
                    ['limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']

                    ]
                );

            if (!$validator->passes()) {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $users = UserFace::where("user_id", "!=", \Auth::user()->user_id)->whereHas('User', function ($query) {
                $query->where('duo_enabled', 1);
            })->with('User')->get();


            foreach ($users as $user)
            {
                $users =  $user->toArray();
                $curated[] = $users['user'];
            }

            return response()->json([
                'status' => TRUE,
                'users' => $curated
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
     * Create a duo invitation
     * @param User $from
     * @param User $to
     */
    private function makeDuoInvitation(User $from, User $to)
    {
        if($to->duo_enabled)
        {
            if(!$challenge = Challenge::where(['object_id' => $to->user_id, 'object_type' => config('constants.CHALLENGE_TYPES_STR.DUO')])->first())
            {
                $challenge = new Challenge();
                $challenge->object_id = $to->user_id;
                $challenge->object_type = config('constants.CHALLENGE_TYPES_STR.DUO');
                $challenge->save();
            }

            if(!$invitation = UserChallenge::where(['user_id' => $from->user_id, 'challenge_id' => $challenge->challenge_id, 'challenge_status' => config('constants.CHALLENGE_STATUS.INVITED')])->first())
            {

                $invitation = new UserChallenge();
                $invitation->user_id = $from->user_id;
                $invitation->challenge_id = $challenge->challenge_id;
                $invitation->challenge_status = config('constants.CHALLENGE_STATUS.INVITED');
                $invitation->save();

                $from->notify(new DuoInvitationNotification($to, $challenge->challenge_id));

            }
        }
    }

    /**
     * Generate daily top users based on challenges completed
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        try
        {
            $result = User::getTopByChallenges(5);

            return response()->json([
                'status' => TRUE,
                'users' => $result->isEmpty() ? [] : $result->toArray()
            ]);

        }
        catch (\Exception $e)
        {
            return response()->json(['status' => FALSE,'report' => $e->getMessage()]);
        }
    }

    /**
     * Generate follow suggestion
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions()
    {
        try
        {
            $limit = Input::get('limit', 10);
            $page = Input::get('page', 0);

            $validator =
                Validator::make(
                    ['limit' => $limit, 'page' => $page],
                    ['limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']

                    ]
                );

            if (!$validator->passes()) {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $following  = \Auth::user()->Following->pluck('following_id');
            $following[] = \Auth::user()->user_id;

            $suggestion =  User::whereNotIn('user_id', $following)->inRandomOrder()->take($limit)->offset($limit*$page)->get();

            return response()->json([
                'status' => TRUE,
                'connections' => $suggestion->isEmpty() ? [] : $suggestion->toArray()
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

    public function facebook_suggestion()
    {
        try
        {
            $limit = Input::get('limit', 10);
            $page = Input::get('page', 0);

            $validator =
                Validator::make(
                    ['limit' => $limit, 'page' => $page],
                    ['limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']

                    ]
                );

            if (!$validator->passes()) {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if(\Auth::user()->facebook_token == null)
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => 'facebook_required'
                ]);
            }

            $fb = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');

            try
            {
                $response = $fb->get('/me/friends?fields=id&limit=5000', \Auth::user()->facebook_token);

                if($response->getHttpStatusCode() == 200)
                {
                    $fbUsers    = \GuzzleHttp\json_decode($response->getBody())->data;

                    $user_ids   = [];
                    $curated    = [];

                    foreach ($fbUsers as $user)
                        $user_ids[] = $user->id;

                    $users = App\Models\UserInformation::has('User')->whereIn('facebook_id', $user_ids)
                        ->with(['User' => function($query) {
                            $following  = \Auth::user()->Following->pluck('following_id');
                            $following[] = \Auth::user()->user_id;
                            $query->whereNotIn('user_id', $following);
                        }])->limit($limit)->offset($limit*$page)->get();

                    foreach ($users as $u)
                        $curated[] = $u->User;

                    return response()->json(['status' => TRUE, 'connections' => $curated]);
                }
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                return response()->json(['status' => FALSE, 'report' =>$e->getMessage()]);
            }

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
     * Generate a batch of duo invitations
     * @param User $user
     * @param int $limit
     * @param int $offset
     */
    private function generateBatchDuoInvitation(User $user, $limit = 20, $offset= 0)
    {
        if($user == null)
        {
            $user = \Auth::user();
        }

        $followers = UserFollower::where(['following_id' => $user->user_id])->whereHas('User', function ($query) {
            $query->where('duo_enabled', 1);
        })->with('User')->limit($limit)->offset($offset)->get();

        foreach($followers as $singleton)
        {
            $this->makeDuoInvitation($singleton->User, $user);
        }
    }
}