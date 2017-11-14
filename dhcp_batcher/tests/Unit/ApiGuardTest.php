<?php

namespace Tests\Unit;

use App\DhcpServer;
use App\Guards\ApiGuard;
use App\Providers\ApiUserProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiGuardTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function valid_credentials_authenticate_against_a_dhcp_server()
    {
        $dhcpServer = factory(DhcpServer::class)->create(['password' => bcrypt('secret'), 'username' => 'test']);
        $apiGuard = new ApiGuard(new ApiUserProvider(), ['username' => 'test', 'password' => 'secret']);
        $this->assertEquals($dhcpServer->id, $apiGuard->user()->id);
    }

    /**
     * @test
     */
    public function invalid_username_fails_to_authenticate_against_a_dhcp_server()
    {
        $dhcpServer = factory(DhcpServer::class)->create(['password' => bcrypt('secret'), 'username' => 'test1']);
        $apiGuard = new ApiGuard(new ApiUserProvider(), ['username' => 'test', 'password' => 'secret']);
        $this->assertNull($apiGuard->user());
    }

    /**
     * @test
     */
    public function invalid_password_fails_to_authenticate_against_a_dhcp_server()
    {
        $dhcpServer = factory(DhcpServer::class)->create(['password' => bcrypt('secret'), 'username' => 'test']);
        $apiGuard = new ApiGuard(new ApiUserProvider(), ['username' => 'test', 'password' => 'secret1']);
        $this->assertNull($apiGuard->user());
    }
}
