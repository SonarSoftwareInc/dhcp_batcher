<?php

namespace App\Services;

use App\PendingDhcpAssignment;

class BatchRequestGenerator
{
    public function generateStructure()
    {
        $assignments = PendingDhcpAssignment::orderBy('created_at','asc')->get();
        foreach ($assignments as $assignment)
        {

        }
    }
}