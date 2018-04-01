<?php

namespace App\Jobs;

use App\Models\Photo;
use App\Notifications\PhotoRevisionNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Log;

class CheckAdultContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $photo;
    protected $adapter;
    private $headers;
    public $tries = 2;

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
            'Ocp-Apim-Subscription-Key' => config('app.oxford_vision_'.$key)
        ];
    }

    /**
     * Execute the job.
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        try {
            if(Photo::find($this->photo->photo_id))
            {
                Log::info("Photo exists");
                $analyze = $this->check($this->photo->url);
                Log::info("Analyze estatus code". $analyze['status_code']);
                if($analyze['status_code'] == 200)
                {
                    if($analyze['response'])
                    {
                        $this->photo->adult_content = 1;
                        $this->photo->touch();
                        $this->photo->save();
                        $this->photo->User->notify(new PhotoRevisionNotification($this->photo));
                    }
                }
                //else throw new Exception('error checking adult content');
            }
        }
        catch(\Exception $e)
        {                
            Log::info($e->getMessage());
        }

    }

    /**
     *
     * Detect photo adult content
     * @param $url
     * @return array
     * @throws Exception
     */
    private function check($url)
    {
        if(empty($url))
            return null;

        $client     = new GuzzleClient(['base_uri' => "https://westus.api.cognitive.microsoft.com/vision/v1.0/"]);

        $adapter    = new GuzzleAdapter($client);
        $request    = new GuzzleRequest('POST', 'analyze?visualFeatures=Adult', $this->headers,json_encode(['url' => $url]));
        $response   = $adapter->sendRequest($request);

        switch ($response->getStatusCode())
        {
            case 200:
                $content = \GuzzleHttp\json_decode($response->getBody()->getContents());
                return ['status_code'=> $response->getStatusCode(), 'response' => $content->adult->isAdultContent];
                break;

            default:
                //throw new Exception('header response error '.$response->getStatusCode().' in analyze()');
                break;
        }
    }
}
