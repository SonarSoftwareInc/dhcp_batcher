<?php

namespace App;

use App\Services\Formatter;
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

    protected $casts = [
        'expired' => 'boolean',
    ];

    public function getLeasedMacAddressAttribute($value)
    {
        $formatter = new Formatter();
        return $formatter->formatMac($value);
    }

    public function getRemoteIdAttribute($value)
    {
        if ($value)
        {
            $formatter = new Formatter();
            return $formatter->formatMac($value);
        }
        return $value;
    }
}
