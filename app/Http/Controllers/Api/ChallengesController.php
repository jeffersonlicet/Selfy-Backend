<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeTodo;
use App\Models\Place;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Validator;
use Gibbo\Foursquare\Client\Client;
use Gibbo\Foursquare\Client\Configuration;
use Gibbo\Foursquare\Client\Entity\Coordinates;
use Gibbo\Foursquare\Client\Factory\Venue\VenueFactory;
use Gibbo\Foursquare\Client\Options\Search;

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
                if(!$place = Place::where('place_external_id', $venue->getIdentifier())->first())
                {
                    $place = new Place();
                    $place->fillFromVenue($venue, [ floatval($venue->getLocation()->getCoordinates()->getLatitude()), floatval($venue->getLocation()->getCoordinates()->getLongitude())]);
                    $place->save();
                }


                $challenge = Challenge::where(['object_id' => $place->place_id, 'object_type' => config('constants.CHALLENGE_TYPES_STR.SPOT')])->first();

                if(!$challenge)
                {
                    $challenge = new Challenge();
                    $challenge->object_type = config('constants.CHALLENGE_TYPES_STR.SPOT');
                    $challenge->object_id   = $place->place_id;
                    $challenge->completed_count = 0;
                    $challenge->saveOrFail();
                }
                $place['challenge'] = $challenge;
                $curated[] = $place;
            }

            return response()->json([
                'status' => TRUE,
                'places' => $curated
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
