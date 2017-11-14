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

        $batchGenerator = new BatchRequestGenerator();
        $structure = $batchGenerator->generateStructure();

        $this->assertEquals(3, PendingDhcpAssignment::count());

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
    public function an_assignment_with_an_identical_leased_mac_and_remote_id_doesnt_generate_the_remote_id()
    {
        $assignment = factory(PendingDhcpAssignment::class)->create(['leased_mac_address' => '00:00:00:00:00:00', 'remote_id' => '00:00:00:00:00:00']);

        $batchGenerator = new BatchRequestGenerator();
        $structure = $batchGenerator->generateStructure();

        $this->assertEquals([
            [
                'expired' => $assignment->expired,
                'ip_address' => $assignment->ip_address,
                'mac_address' => $assignment->leased_mac_address,
                'remote_id' => null
            ]
        ], $structure);
    }
}
