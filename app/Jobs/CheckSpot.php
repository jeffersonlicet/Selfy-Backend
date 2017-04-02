<?php

namespace App\Jobs;

use App\Models\Challenge;
use App\Models\ChallengeCompleted;
use App\Models\Photo;
use App\Models\Place;
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
        /* Check if the place exists */
        $this->place();
        
        /* Check if the photo meets a challenge */
        $this->photo();
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
            if(!$place = Place::where(['latitude' => $this->coordinates[0], 'longitude' => $this->coordinates[1]])->first())
            {
                $client = Client::simple(new Configuration( config('app.foursquare_client'), config('app.foursquare_secret')), VenueFactory::simple());

                $options = Search::coordinates(new Coordinates($this->coordinates[0], $this->coordinates[1]))
                    ->limit(1)
                    ->radius(15);

                $venues = $client->search($options);

                if(isset($venues[0]))
                {
                    $place = new Place();
                    $place->fillFromVenue($venues[0], $this->coordinates);
                    $place->save();
                }
            }

           elseif(strtotime($place->updated_at) < strtotime('-30 days'))
            {
                $place->fillFromVenue($venues[0]);
                $place->save();
            }

            $this->place = $place;
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

            $challenge = new Challenge();
            $challenge->object_type = config('constants.CHALLENGE_TYPES_STR.SPOT');
            $challenge->object_id   = $this->place->place_id;
            $challenge->saveOrFail();

            $completed = new ChallengeCompleted();
            $completed->photo_id        = $this->photo->photo_id;
            $completed->user_id         = $this->photo->user_id;
            $completed->challenge_id    = $challenge->challenge_id;
            $completed->saveOrFail();
        }

        catch(\Exception $e)
        {
            Log::info($e);
        }

    }
}
