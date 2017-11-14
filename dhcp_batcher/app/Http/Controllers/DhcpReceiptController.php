<?php

namespace App\Http\Controllers;

use App\Http\Requests\DhcpReceiptRequest;
use App\PendingDhcpAssignment;
use Illuminate\Http\Request;

class DhcpReceiptController extends Controller
{
    /**
     * @param DhcpReceiptRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function receiveDhcpAssignment(DhcpReceiptRequest $request)
    {
        $pendingDhcpAssignment = new PendingDhcpAssignment([
            'leased_mac_address' => request('leased_mac_address'),
            'ip_address' => request('ip_address'),
            'remote_id' => request('remote_id'),
            'expired' => request('expired'),
        ]);
        $pendingDhcpAssignment->save();

        return response()->json([
            'success' => true
        ], 200);
    }
}
