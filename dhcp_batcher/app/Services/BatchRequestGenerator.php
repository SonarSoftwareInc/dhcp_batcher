<?php

namespace App\Services;

use App\PendingDhcpAssignment;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class BatchRequestGenerator
{
    private $assignments;

    public function generateStructure()
    {
        $structure = [];

        $this->assignments = PendingDhcpAssignment::orderBy('created_at','asc')->get();
        foreach ($this->assignments as $assignment)
        {
            $structure[$this->generateHash($assignment)] = [
                'expired' => $assignment->expired,
                'ip_address' => $assignment->ip_address,
                'mac_address' => $assignment->leased_mac_address,
                'remote_id' => $assignment->remote_id === $assignment->leased_mac_address ? null : $assignment->remote_id,
            ];
        }

        return array_values($structure);
    }

    /**
     * Send the assignments over to Sonar
     */
    public function send()
    {
        $structure = $this->generateStructure();
        $httpClient = App::make('HttpClient');
        try {
            $response = $httpClient->post(config("sonar.url") . "/api/v1/network/ipam/batch_dynamic_ip_assignment",[
                'auth' => [
                    config("sonar.username"),
                    config("sonar.password")
                ],
                'json' => $structure
            ]);

            $this->deletePendingAssignments();
        }
        catch (Exception $e)
        {
            Log::error("Failure when communicating with Sonar: " . $e->getMessage());
        }
    }

    /**
     * Delete the processed assignments.
     */
    private function deletePendingAssignments()
    {
        $ids = $this->assignments->pluck('id')->toArray();
        $chunks = array_chunk($ids, 50000);
        foreach ($chunks as $chunk)
        {
            PendingDhcpAssignment::whereIn('id',$chunk)->delete();
        }
    }

    /**
     * @param PendingDhcpAssignment $assignment
     * @return string
     */
    private function generateHash(PendingDhcpAssignment $assignment):string
    {
        return md5($assignment->leased_mac_address . $assignment->ip_address);
    }
}