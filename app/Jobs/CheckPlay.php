<?php

namespace App\Jobs;

use App\Helpers\Vision;
use App\Models\Challenge;
use App\Models\ChallengePlay;
use App\Models\Photo;
use App\Models\ObjectCategory;
use App\Models\PlayObject;
use App\Models\UserChallenge;
use App\Notifications\PlayNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class CheckPlay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $photo;

    /**
     * Create a new job instance.
     *
     * @param Photo $photo
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $response = Vision::recognize($this->photo);
        $creator = $this->photo->User;
        switch ($response->getStatusCode())
        {
            case 200:

                $content = \GuzzleHttp\json_decode($response->getBody()->getContents());

                if($content->status)
                {
                    $words = $content->content;
                    $check = 0;
                    foreach ($words as $word)
                    {
                        /**
                         * Limit to two words
                         */
                        if($check == 2) break;

                        /**
                         * If we have a word in db then let the magic begin
                         */
                        if($object = ObjectCategory::where('category_name', $word)->first())
                        {
                            $parent = $object;

                            while ($parent != null)
                            {
                                /**
                                 *  Search for a PlayObject
                                 *  A PlayObject is a relation between a ObjectCategory and a PlayChallenge
                                 */
                                if($playObject = PlayObject::where(['category_id' => $parent->category_id])->first())
                                {
                                    /**
                                     *  Search for a PlayChallenge
                                     *  A PlayChallenge is a Challenge description for a Challenge.
                                     */
                                    if($play = ChallengePlay::find($playObject->play_id))
                                    {
                                        /**
                                         *  Search for a Challenge
                                         *  Of course, a challenge associated with the PlayChallenge
                                         */
                                        $challenge = Challenge::where([
                                            'object_type'=> config('constants.CHALLENGE_TYPES_STR.PLAY'),
                                            'object_id'=> $play->play_id])
                                            ->first();

                                        // if the challenge exists
                                        if($challenge)
                                        {
                                            //Complete it
                                            $complete = new UserChallenge();
                                            $complete->challenge_id = $challenge->challenge_id;
                                            $complete->user_id = $creator->user_id;
                                            $complete->photo_id = $this->photo->photo_id;
                                            $complete->challenge_status = config('constants.CHALLENGE_STATUS.COMPLETED');
                                            $complete->save();

                                            $challenge->completed_count++;
                                            $challenge->save();

                                            $creator->play_completed++;
                                            $creator->save();

                                            //Dispatch notification
                                            $creator->notify(new PlayNotification($this->photo));
                                        }
                                    }
                                }

                                $parent = $parent->parent;
                            }
                        }

                        $check++;
                    }

                }

                break;
        }
    }
}
