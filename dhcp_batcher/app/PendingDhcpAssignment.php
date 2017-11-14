<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingDhcpAssignment extends Model
{
    protected $dateFormat = "Y-m-d H:i:s.u";

    protected $fillable = [
        'leased_mac_address',
        'ip_address',
        'remote_id',
        'expired',
    ];
}
