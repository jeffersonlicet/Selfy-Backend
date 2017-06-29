<?php
namespace App\Helpers;
use App\Models\Photo;
use Log;
use Mockery\Exception;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
/**
 * Class Expression
 *
 * Generate regular expression helpers for social integration
 * @package App\Helpers
 */
class Vision
{
    /**
     * Perform image object detection using our VM
     *
     * @param Photo $photo
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public static function recognize(Photo $photo)
    {
        try
        {
            $client     = new GuzzleClient(['base_uri' => "http://starbits.southcentralus.cloudapp.azure.com/api/"]);
            $adapter    = new GuzzleAdapter($client);

            $request    = new GuzzleRequest('POST', 'detect', ["content-type" => 'application/json'],
                json_encode(['photo_url' => $photo->url]));

           return $adapter->sendRequest($request);
        }
        catch(Exception $ex)
        {
            Log::info("VM is disabled" . $ex->getMessage());
            return null;
        }
    }
}