<?php

namespace App\Http\Controllers\Api;

use App\Models\UserFollower;
use App\Models\UserInvitation;
use App\Notifications\AcceptedInvitationNotification;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\Input;
class InvitationController extends Controller
{
    /**
     * Get a collection of user follow invitations
     * @return \Illuminate\Http\JsonResponse
     * @internal param Request $request
     */
    public function index()
    {
        try
        {
            $page = Input::get('page', 0);
            $limit = Input::get('limit', config('app.likes_per_page'));
            $status = Input::get('status', config('constants.INVITATION_STATUS.INVITED'));

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

            $invitations = UserInvitation::with('Creator')->where(['profile_id' => \Auth::user()->user_id, 'invitation_status' => $status])->offset($page*$limit)->limit($limit)->get();

            return response()->json([
                'status' => TRUE,
                'invitations' => $invitations->isEmpty() ? [] : $invitations->toArray()
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
     * Accept a user follow invitation
     * @param Request $request
     * @throws Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'user_id' => ['required', 'numeric']
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if(!$invitation = UserInvitation::where([
                'user_id' => $input['user_id'],
                'profile_id' => \Auth::user()->user_id,
                'invitation_status' => config('constants.INVITATION_STATUS.INVITED')])->first())
            {
                throw new Exception("resource_not_found");
            }

            $invitation->invitation_status = config('constants.INVITATION_STATUS.ACCEPTED');
            $invitation->touch();
            $invitation->save();

            $connection = new UserFollower();
            $connection->follower_id = $invitation->user_id;
            $connection->following_id = $invitation->profile_id;
            $connection->save();

            \Auth::user()->followers_count++;
            \Auth::user()->save();

            $invitation->Creator->following_count++;
            $invitation->Creator->save();

            $invitation->Creator->notify(new AcceptedInvitationNotification(\Auth::user()));

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
     * Recreate follow invitation
     * @param Request $request
     * @throws Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'user_id' => ['required', 'numeric']
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if($invitation = UserInvitation::where([
                'user_id' => $input['user_id'],
                'profile_id' => \Auth::user()->user_id])->first())
            {
                $invitation->invitation_status = config('constants.INVITATION_STATUS.ACCEPTED');
                $invitation->touch();
                $invitation->save();

            }
            else
            {
                $invitation = new UserInvitation();
                $invitation->invitation_status = config('constants.INVITATION_STATUS.ACCEPTED');
                $invitation->user_id = $input['user_id'];
                $invitation->profile_id = \Auth::user()->user_id;
                $invitation->save();
            }

            $connection = new UserFollower();
            $connection->follower_id = $invitation->user_id;
            $connection->following_id = $invitation->profile_id;
            $connection->save();

            \Auth::user()->followers_count++;
            \Auth::user()->save();

            $invitation->Creator->following_count++;
            $invitation->Creator->save();

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
     * Decline a user follow invitation
     * @param Request $request
     * @throws Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function decline(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'user_id' => ['required', 'numeric']
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if(!$invitation = UserInvitation::where([
                'user_id' => $input['user_id'],
                'profile_id' => \Auth::user()->user_id,
                'invitation_status' => config('constants.INVITATION_STATUS.INVITED')])->first())
            {
                throw new Exception("resource_not_found");
            }

            $invitation->invitation_status = config('constants.INVITATION_STATUS.DECLINED');
            $invitation->touch();
            $invitation->save();

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
     * Delete an invitation and if exist, the follower
     * @param Request $request
     * @throws Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request)
    {
        try
        {
            $input = $request->all();

            $validator = Validator::make($input, [
                'user_id' => ['required', 'numeric']
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            if(!$invitation = UserInvitation::where([
                'user_id' => $input['user_id'],
                'profile_id' => \Auth::user()->user_id])->first())
            {
                throw new Exception("resource_not_found");
            }

            if($followingDefinition = UserFollower::where(['follower_id' => $invitation->user_id, 'following_id' => \Auth::user()->user_id])->first()) {
                \Auth::user()->followers_count--;
                \Auth::user()->save();

                $invitation->Creator->following_count--;
                $invitation->Creator->save();

                $followingDefinition->delete();
            }

            $invitation->delete();

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
}
