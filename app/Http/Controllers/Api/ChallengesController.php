<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Place;
use App\Models\UserChallenge;
use Exception;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Validator;
use Gibbo\Foursquare\Client\Client;
use Gibbo\Foursquare\Client\Configuration;
use Gibbo\Foursquare\Client\Entity\Coordinates;
use Gibbo\Foursquare\Client\Factory\Venue\VenueFactory;
use Gibbo\Foursquare\Client\Options\Search;

/**
 * Class ChallengesController
 * @package App\Http\Controllers\Api
 */
class ChallengesController extends Controller
{
    /**
     * Show a challenge
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
                    'likes' => $validator->messages()->first()
                ]);
            }
            
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
     * Get near places and challenges
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function nearby()
    {
        try
        {
            $latitude    = Input::get('latitude' );
            $longitude   = Input::get('longitude' );
            $limit   = Input::get('limit', config('app.photos_best_per_page'));
            $page    = Input::get('page', 0);

            $validator =
                Validator::make(
                    ['latitude' => $latitude, 'longitude'=>$longitude,  'limit' => $limit, 'page'=> $page],
                    ['latitude' => ['required'], 'longitude' => ['required'], 'limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric']]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $client = Client::simple(new Configuration( config('app.foursquare_client'), config('app.foursquare_secret')), VenueFactory::simple());

            $options = Search::coordinates(new Coordinates( floatval($latitude), floatval($longitude)))
                ->limit(7);

            $venues = $client->search($options);
            $curated = [];

            foreach ($venues as $venue)
            {
                if(!$place = Place::where('place_external_id', trim($venue->getIdentifier()))->first())
                {
                    $place = new Place();
                    $place->fillFromVenue($venue, [ floatval($venue->getLocation()->getCoordinates()->getLatitude()), floatval($venue->getLocation()->getCoordinates()->getLongitude())]);
                    $place->save();
                }

                $challenge = Challenge::where(['object_id' => $place->place_id, 'object_type' => config('constants.CHALLENGE_TYPES_STR.SPOT')])->first();

                if($challenge)
                {
                    $curated[] = $challenge;
                }
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
     * Accept a challenge invitation
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
                'challenge_id' => ['required', 'numeric']
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }


            if(!$invitation = UserChallenge::where(['challenge_id' => $input['challenge_id'], 'user_id' => \Auth::user()->user_id])->first())
            {
                throw new Exception("resource_not_found");
            }

            if($invitation->challenge_status == config('constants.CHALLENGE_STATUS.ACCEPTED'))
            {
                throw new Exception("invalid_action");
            }

            if((\Auth::user()->duo_todo + \Auth::user()->spot_todo + \Auth::user()->play_todo) == 5)
            {
                throw new Exception("limit_reached");
            }

            $invitation->challenge_status = config('constants.CHALLENGE_STATUS.ACCEPTED');

            $invitation->save();

            $increment = $invitation->Challenge->object_type."_todo";
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
     * Decline a challenge invitation
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
                'challenge_id' => ['required', 'numeric']
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }


            if(!$invitation = UserChallenge::where(['challenge_id' => $input['challenge_id'], 'user_id' => \Auth::user()->user_id])->first())
            {
                throw new Exception("resource_not_found");
            }

            if($invitation->challenge_status == config('constants.CHALLENGE_STATUS.DECLINED'))
            {
                throw new Exception("invalid_action");
            }

            $invitation->challenge_status = config('constants.CHALLENGE_STATUS.DECLINED');

            $invitation->save();

            if($invitation->challenge_status == config('constants.CHALLENGE_STATUS.ACCEPTED'))
            {
                $increment = $invitation->Challenge->object_type."_todo";
                \Auth::user()->{$increment}--;
                \Auth::user()->save();
            }

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
     * Remove a challenge invitation
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
                'challenge_id' => ['required', 'numeric']
            ]);

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }


            if(!$invitation = UserChallenge::where(['challenge_id' => $input['challenge_id'], 'user_id' => \Auth::user()->user_id])->first())
            {
                throw new Exception("resource_not_found");
            }

            if($invitation->challenge_status == config('constants.CHALLENGE_STATUS.ACCEPTED'))
            {
                $increment = $invitation->Challenge->object_type."_todo";
                \Auth::user()->{$increment}--;
                \Auth::user()->save();
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

    /**
     * Get top photos that meets the $challenge_id challenge
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function top_photos()
    {
        try
        {
            $limit   = Input::get('limit', config('app.photos_best_per_page'));
            $page    = Input::get('page', 0);
            $challenge = Input::get('challenge_id');

            $validator = Validator::make(['limit' => $limit, 'page'=> $page, 'challenge_id' => $challenge],
                    ['limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric'],
                        'challenge_id' => 'required|numeric']
                );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $result = UserChallenge::where('challenge_id', $challenge)->has('Photo')->with(['Photo' => function ($q) {
                $q->orderBy('likes_count', 'desc')->orderBy('views_count', 'desc')->orderBy('comments_count', 'desc')->whereHas('User', function($z){
                    $z->where('account_private', '=', 0);
                });
            }])->limit($limit)->offset($page*$limit)->get();

            $curated = [];

            foreach ($result as $item)
            {
                $curated[] = $item->Photo;
            }

            return response()->json([
                'status' => TRUE,
                'photos' => $curated
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
     * Get recent photos that meets the $challenge_id challenge
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recent_photos()
    {
        try
        {
            $limit   = Input::get('limit', config('app.photos_best_per_page'));
            $page    = Input::get('page', 0);
            $challenge = Input::get('challenge_id');

            $validator = Validator::make(['limit' => $limit, 'page'=> $page, 'challenge_id' => $challenge],
                ['limit' => ['required', 'numeric', 'between:1,20'], 'page' => ['required', 'numeric'],
                    'challenge_id' => 'required|numeric']
            );

            if(!$validator->passes())
            {
                return response()->json([
                    'status' => TRUE,
                    'report' => $validator->messages()->first()
                ]);
            }

            $result = UserChallenge::where('challenge_id', $challenge)->has('Photo')->with(['Photo' => function($q){
                $q->whereHas('User', function($z){
                    $z->where('account_private', '=', 0);
                });
            }])->limit($limit)->offset($page*$limit)->get();

            $curated = [];

            foreach ($result as $item)
            {
                $curated[] = $item->Photo;
            }

            return response()->json([
                'status' => TRUE,
                'photos' => $curated
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
