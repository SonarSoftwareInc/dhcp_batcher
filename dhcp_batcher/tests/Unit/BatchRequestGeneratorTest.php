<?php

namespace Tests\Unit;

use App\PendingDhcpAssignment;
use App\Services\BatchRequestGenerator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BatchRequestGeneratorTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function request_generated_contains_all_pending_assignments()
    {
        $assignments = factory(PendingDhcpAssignment::class, 2)->create();
        $batchGenerator = new BatchRequestGenerator();
        $this->assertEquals([
            [
                'expired' => $assignments[0]->expired,
                'ip_address' => $assignments[0]->ip_address,
                'mac_address' => $assignments[0]->leased_mac_address,
                'remote_id' => $assignments[0]->remote_id,
            ],
            [
                'expired' => $assignments[1]->expired,
                'ip_address' => $assignments[1]->ip_address,
                'mac_address' => $assignments[1]->leased_mac_address,
                'remote_id' => $assignments[1]->remote_id,
            ]
        ], $batchGenerator->generateStructure());
    }

    /**
     * @test
     */
    public function duplicate_assignments_are_overwritten_by_a_later_one()
    {
        $assignments = factory(PendingDhcpAssignment::class, 2)->create(['expired' => false]);

        $duplicateAssignment = new PendingDhcpAssignment([
            'leased_mac_address' => $assignments[1]->leased_mac_address,
            'ip_address' => $assignments[1]->ip_address,
            'expired' => true,
        ]);
        $duplicateAssignment->save();
        
        $this->assertEquals(3, PendingDhcpAssignment::count());

        $batchGenerator = new BatchRequestGenerator();
        $structure = $batchGenerator->generateStructure();

        $this->assertEquals([
            [
                'expired' => $assignments[0]->expired,
                'ip_address' => $assignments[0]->ip_address,
                'mac_address' => $assignments[0]->leased_mac_address,
                'remote_id' => $assignments[0]->remote_id,
            ],
            [
                'expired' => $duplicateAssignment->expired,
                'ip_address' => $duplicateAssignment->ip_address,
                'mac_address' => $duplicateAssignment->leased_mac_address,
                'remote_id' => $duplicateAssignment->remote_id,
            ]
        ], $structure);

        $this->assertCount(2, $structure);
    }

    /**
     * @test
     */
    public function generating_a_request_removes_pending_requests()
    {
        factory(PendingDhcpAssignment::class, 2)->create();
        $batchGenerator = new BatchRequestGenerator();
        $this->assertCount(2, $batchGenerator->generateStructure());
        $this->assertCount(0, $batchGenerator->generateStructure());
    }
}
