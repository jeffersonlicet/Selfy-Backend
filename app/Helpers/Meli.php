<?php
namespace App\Helpers;
use App\Models\Photo;
use App\Models\TargetProduct;
use Log;
use Mockery\Exception;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
/**
 * Class Meli
 *
 * Meli wrapper
 * @package App\Helpers
 */
class Meli
{
    private static $endpoint = 'https://api.mercadolibre.com/sites/MLV/';

    public static function search(TargetProduct $target)
    {
        try
        {
            $client     = new GuzzleClient(['base_uri' => self::$endpoint]);
            $adapter    = new GuzzleAdapter($client);

            $request    = new GuzzleRequest(
                'GET',
                'search?q='.$target->name.'&offset='.$target->page*$target->limit.'&limit='.$target->limit);

            $response = $adapter->sendRequest($request);

            if($response->getStatusCode() == 200)
            {
                $content = \GuzzleHttp\json_decode($response->getBody()->getContents());

                if($content->paging->total >= $target->page*$target->limit){
                    $target->page++;
                } else {
                    $target->page = 0;
                }

                $target->save();

                return $content->results;
            }

            return null;
        }
        catch(Exception $ex)
        {
            Log::info("Error searching" . $ex->getMessage());
            return null;
        }
    }
}