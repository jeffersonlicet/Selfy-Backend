<?php

namespace App\Jobs;

use App\Models\Challenge;
use App\Models\Photo;
use App\Models\Place;
use App\Models\UserChallenge;
use App\Notifications\SpotNotification;
use Gibbo\Foursquare\Client\Client;
use Gibbo\Foursquare\Client\Configuration;
use Gibbo\Foursquare\Client\Entity\Coordinates;
use Gibbo\Foursquare\Client\Factory\Venue\VenueFactory;
use Gibbo\Foursquare\Client\Options\Search;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class CheckSpot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
   
    protected $photo;
    protected $coordinates;
    protected $place;

    /**
     * Create a new job instance.
     *
     * @param Photo $photo
     * @param $coordinates
     *
     *
     */
    public function __construct($photo, $coordinates = NULL)
    {
        $this->photo = $photo;
        $this->coordinates = $coordinates;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!Photo::find($this->photo->photo_id)) return;

        /* Check if the place exists */
        $this->place();
    }

    /**
     * Save or update a place
     *
     * @return void
     */
    private function place()
    {
        try
        {

            $place = Place::where(['latitude' => $this->coordinates[0],
                'longitude' => $this->coordinates[1],
                'status' => config('constants.PLACE_STATUS.challenge')])->first();

            if(!$place)
            {
                $client = Client::simple(new Configuration( config('app.foursquare_client'), config('app.foursquare_secret')), VenueFactory::simple());
                $options = Search::coordinates(new Coordinates($this->coordinates[0], $this->coordinates[1]))
                    ->limit(1)
                    ->radius(15);

                $venues = $client->search($options);

                if(isset($venues[0]))
                {
                    if(!$place = Place::where('place_external_id', trim($venues[0]->getIdentifier()))->first()) {
                        $place = new Place();
                        $place->fillFromVenue($venues[0], $this->coordinates);
                        $place->save();
                    }
                }
            }

           elseif(strtotime($place->updated_at) < strtotime('-30 days'))
            {
                $client = Client::simple(new Configuration( config('app.foursquare_client'), config('app.foursquare_secret')), VenueFactory::simple());
                $options = Search::coordinates(new Coordinates($this->coordinates[0], $this->coordinates[1]))
                    ->limit(1)
                    ->radius(15);

                $venues = $client->search($options);

                if(isset($venues[0]))
                {
                    $place->fillFromVenue($venues[0]);
                    $place->save();

                    /* Check if the photo meets a challenge */
                    $this->place = $place;
                    $this->photo();
                }

            } else {
                /* Check if the photo meets a challenge */
                $this->place = $place;
                $this->photo();
            }


        }
        catch (\Exception $e)
        {
            Log::info($e);
        }

    }

    /**
     * Bind photo with challenge and place
     *
     * @return void
     */
    private function photo()
    {
        try
        {
            $this->photo->place_id = $this->place->place_id;
            $this->photo->saveOrFail();

            $challenge = Challenge::where(['object_id' => $this->place->place_id, 'object_type' => config('constants.CHALLENGE_TYPES_STR.SPOT')])->first();

            if($challenge)
            {
                $challenge->completed_count++;
                $challenge->save();

                $completed = new UserChallenge();
                $completed->photo_id        = $this->photo->photo_id;
                $completed->challenge_id    = $challenge->challenge_id;
                $completed->user_id = $this->photo->User->user_id;
                $completed->challenge_status = config('constants.CHALLENGE_STATUS.COMPLETED');
                $completed->saveOrFail();

                $this->photo->User->spot_completed++;
                $this->photo->User->save();
                $this->photo->User->notify(new SpotNotification($this->photo->photo_id));
            }
        }

        catch(\Exception $e)
        {
            Log::info($e);
        }

    }
}
