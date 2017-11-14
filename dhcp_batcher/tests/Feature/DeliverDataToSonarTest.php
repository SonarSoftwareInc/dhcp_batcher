<?php

namespace Tests\Feature;

use App\PendingDhcpAssignment;
use App\Services\BatchRequestGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeliverDataToSonarTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function a_successful_delivery_deletes_pending_assignments()
    {
        $mock = new MockHandler([
            new Response(200),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        App::instance('HttpClient', $client);

        $assignments = factory(PendingDhcpAssignment::class,10)->create();
        $batchGenerator = new BatchRequestGenerator();
        $batchGenerator->send();

        $this->assertCount(0, PendingDhcpAssignment::whereIn('id',$assignments->pluck('id')->toArray())->get());
    }

    /**
     * @test
     */
    public function a_failed_delivery_does_not_delete_pending_assignments()
    {
        $mock = new MockHandler([
            new Response(422),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        App::instance('HttpClient', $client);

        $assignments = factory(PendingDhcpAssignment::class,10)->create();
        $batchGenerator = new BatchRequestGenerator();
        $batchGenerator->send();

        $this->assertCount(10, PendingDhcpAssignment::whereIn('id',$assignments->pluck('id')->toArray())->get());
    }
}
