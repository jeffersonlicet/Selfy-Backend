<?php

namespace App\Jobs;

use App\Models\ChallengeCompleted;
use App\Models\Photo;
use App\Models\UserFaceRecognition;
use App\Notifications\DuoNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request as GuzzleRequest;


class CheckDuo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $photo;
    protected $adapter;
    private $headers;


    /**
     * Create a new job instance.
     * @param Photo $photo
     * @param $key
     */
    public function __construct(Photo $photo, $key)
    {
        $this->photo = $photo;

        $this->headers =  [
            'Content-Type'=> 'application/json',
            'Ocp-Apim-Subscription-Key' => config('app.oxford_'.$key)
        ];

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if(Photo::find($this->photo->photo_id))
        /** @noinspection end */
        {
            $creator = $this->photo->User;

            /*
             *  Be sure the user has to do challenges and a face associated
             */
            if(count($creator->Todo) > 0 && $creator->Face != null)
            {
                /*
                 * Check if the photo has faces
                 */
                $response = $this->detect($this->photo->url);

                if($response['status_code'] == 200 and $response['faces_count'] == 2)
                {

                    /* Get user Descriptor */
                    if (count($creator->FaceDescriptors) == 0 or (count($creator->FaceDescriptors) > 0 and  $creator->FaceDescriptors[0]->updated_at < Carbon::now()->subHours(23)))
                    {

                        $trainCreator = $this->detect($creator->Face->url);

                        if ($trainCreator['status_code'] == 200 and $trainCreator['faces_count'] > 0)
                        {
                            if (count($creator->FaceDescriptors) == 0) {
                                $descriptor = new UserFaceRecognition();
                                $descriptor->face_external_id = $trainCreator['faces'][0]->faceId;
                                $descriptor->face_id = $creator->Face->face_id;
                                $descriptor->save();
                                $creator->FaceDescriptors[0] = $descriptor;
                            } else {
                                $creator->FaceDescriptors[0]->face_external_id = $trainCreator['faces'][0]->faceId;
                                $creator->FaceDescriptors[0]->touch();
                                $creator->FaceDescriptors[0]->save();
                            }
                        }
                    }

                    /* Check if the user is in the photo */
                    $creatorBelongs = FALSE;

                    /* Index of face in the photo that belongs to the user */
                    $k = 0;

                    foreach($response['faces'] as $face)
                    {
                        $identification = $this->identify([$creator->FaceDescriptors[0]->face_external_id, $face->faceId]);

                        if($identification['status_code'] == 200 and $identification['identical'])
                        {
                            /* Remove to avoid check again with candidates*/
                            unset($response['faces'][$k]);

                            $creatorBelongs = TRUE;
                            break;
                        }

                        $k++;
                    }

                    $response['faces'] = array_values($response['faces']);

                    /*  Creator is in the photo and the photo have now 1 faces */
                    if($creatorBelongs)
                    {

                        /* Check every candidate */
                        foreach($creator->Todo as $single)
                        {
                            if($single->Challenge->object_type != config('constants.CHALLENGE_TYPES_STR.DUO') or $single->Challenge->Object->Face == null)
                                continue;

                            $friend = $single->Challenge->Object;

                            if (count($friend->FaceDescriptors) == 0 or (count($friend->FaceDescriptors) > 0 && $friend->FaceDescriptors[0]->updated_at < Carbon::now()->subHours(23)))
                            {
                                $trainFriend = $this->detect($friend->Face->url);

                                if ($trainFriend['status_code'] == 200 and $trainFriend['faces_count'] > 0)
                                {
                                    if (count($friend->FaceDescriptors) == 0) {
                                        $descriptor = new UserFaceRecognition();
                                        $descriptor->face_external_id = $trainFriend['faces'][0]->faceId;
                                        $descriptor->face_id = $friend->Face->face_id;
                                        $descriptor->save();
                                        $friend->FaceDescriptors[0] = $descriptor;
                                    } else {
                                        $friend->FaceDescriptors[0]->face_external_id = $trainFriend['faces'][0]->faceId;
                                        $friend->FaceDescriptors[0]->save();
                                    }
                                }
                            }

                            $identification = $this->identify([$friend->FaceDescriptors[0]->face_external_id, $response['faces'][0]->faceId]);

                            if($identification['status_code'] == 200 and $identification['identical'])
                            {
                                /** @noinspection PhpUndefinedMethodInspection */
                                if(!$completed = ChallengeCompleted::where(['photo_id' => $this->photo->photo_id, 'challenge_id' => $single->Challenge->challenge_id])->first())
                                 /** @noinspection end */
                                {


                                    $completed = new ChallengeCompleted();
                                    $completed->challenge_id = $single->Challenge->challenge_id;
                                    $completed->photo_id = $this->photo->photo_id;
                                    $completed->user_id = $creator->user_id;
                                    $completed->save();

                                    $single->Challenge->completed_count++;
                                    $single->Challenge->save();

                                    $creator->notify(new DuoNotification($this->photo->photo_id));
                                    break;
                                }
                            }
                        }
                    }
                }
                else { print("The photo does not have 2 faces"); }
            }
        }
    }

    /**
     *
     * Detect faces in photo
     * @param $url
     * @return array
     * @throws Exception
     */
    private function detect($url)
    {
        if(empty($url))
            return null;

        $client     = new GuzzleClient(['base_uri' => "https://westus.api.cognitive.microsoft.com/face/v1.0/"]);

        $adapter    = new GuzzleAdapter($client);
        $request    = new GuzzleRequest('POST', 'detect', $this->headers,json_encode(['url' => $url, 'returnFaceAttributes' => true]));
        $response   = $adapter->sendRequest($request);

        switch ($response->getStatusCode())
        {
            case 200:
                $content = \GuzzleHttp\json_decode($response->getBody()->getContents());
                return ['status_code'=> $response->getStatusCode(), 'faces_count' => count((array)$content),'faces' => $content];
                break;

            default:
                throw new Exception('header response error '.$response->getStatusCode().' in identify()');
                break;
        }
    }

    /**
     *
     * Check if two faces are identical
     * @param $faces
     * @return array
     * @throws Exception
     */
    private function identify($faces)
    {
        if(!is_array($faces))
            return null;

        $client     = new GuzzleClient(['base_uri' => "https://westus.api.cognitive.microsoft.com/face/v1.0/"]);

        $adapter    = new GuzzleAdapter($client);
        $request    = new GuzzleRequest('POST', 'verify', $this->headers,json_encode(['faceId1' => $faces[0], 'faceId2' => $faces[1]]));
        $response   = $adapter->sendRequest($request);

        switch ($response->getStatusCode())
        {
            case 200:
                $content = \GuzzleHttp\json_decode($response->getBody()->getContents());
                return ['status_code'=> $response->getStatusCode(), 'identical' => $content->isIdentical];
                break;

            default:
                throw new Exception('header response error '.$response->getStatusCode().' in identify()');
                break;
        }
    }
}