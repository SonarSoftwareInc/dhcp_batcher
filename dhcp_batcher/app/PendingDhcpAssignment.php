<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingDhcpAssignment extends Model
{
    protected $fillable = [
        'leased_mac_address',
        'ip_address',
        'remote_id',
        'expired',
    ];
}
