<?php
namespace App\Helpers;
use App\Models\Photo;
use Mockery\Exception;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
/**
 * Class Face
 *
 * Face detection and recognition using Microsoft Cognitive Services
 * @package App\Helpers
 */
class Face
{
    /**
     * Perform face detection
     *
     * @param Photo $photo
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function detect(Photo $photo)
    {
       return [];
    }

    /**
     * Perform face recognition
     *
     * @param Photo $photo
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function recognize(Photo $photo)
    {
        return [];
    }
}