<?php

namespace Tests\Feature;

use App\Helpers\Vision;
use App\Models\Photo;
use Auth;
use Tests\TestCase;


class ApiPhotoTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Auth::loginUsingId(1);
    }

    public function testCredentialsRequired()
    {
        Auth::logout();
        $response = $this->call('GET', 'api/photos')->json();

        $this->assertEquals(false, $response['status']);
        $this->assertEquals('token_absent', $response['report']);
    }

    public function testVisionVM()
    {
        $photo = new Photo();
        $photo->url = 'http://i.imgur.com/VzIAMHG.jpg';

        $response = Vision::recognize($photo);
        $this->assertEquals(200, $response->getStatusCode());

        $content = \GuzzleHttp\json_decode($response->getBody()->getContents());
        $this->assertEquals(true, $content->status);
        $this->assertEquals('beagle', $content->content[0]);
    }

}
