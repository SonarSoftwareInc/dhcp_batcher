<?php

namespace Tests\Feature;

use App\PendingDhcpAssignment;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FlushPendingAssignmentsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function flushing_dhcp_assignments_removes_them()
    {
        factory(PendingDhcpAssignment::class,5)->create();

        $this->assertCount(5, PendingDhcpAssignment::all());

        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get("/flush")
            ->assertStatus(302);

        $this->assertCount(0, PendingDhcpAssignment::all());
    }
}
