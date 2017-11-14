<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestSonarCredentialsTest extends TestCase
{
    /**
     * @test
     */
    public function valid_credentials_succeed()
    {
        $mock = new MockHandler([
            new Response(200),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        App::instance('HttpClient', $client);

        $this->assertEquals(0, Artisan::call("sonar:test"));
    }

    /**
     * @test
     */
    public function invalid_credentials_fail()
    {
        $mock = new MockHandler([
            new Response(401),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        App::instance('HttpClient', $client);

        $this->assertEquals(1, Artisan::call("sonar:test"));
    }
}
