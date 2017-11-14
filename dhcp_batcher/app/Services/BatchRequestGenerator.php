<?php

namespace App\Services;

use App\PendingDhcpAssignment;

class BatchRequestGenerator
{
    public function generateStructure()
    {
        $structure = [];

        $assignments = PendingDhcpAssignment::orderBy('created_at','asc')->get();
        foreach ($assignments as $assignment)
        {
            $structure[$this->generateHash($assignment)] = [
                'expired' => $assignment->expired,
                'ip_address' => $assignment->ip_address,
                'mac_address' => $assignment->leased_mac_address,
                'remote_id' => $assignment->remote_id,
            ];
        }

        $ids = $assignments->pluck('id')->toArray();
        $chunks = array_chunk($ids, 50000);
        foreach ($chunks as $chunk)
        {
            PendingDhcpAssignment::whereIn('id',$chunk)->delete();
        }

        return array_values($structure);
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