<?php

namespace Tests\Feature;

use Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
        $response = json_decode($this->call('GET', 'api/photos'));

        $this->assertEquals(false, $response->status);
        $this->assertEquals('token_absent', $response->report);
    }

}
