<?php

namespace App\Services;

use App\Exceptions\FormattingException;

class Formatter
{
    /**
     * Format a MAC address into a standardized format
     * @param $mac
     * @return string
     */
    public function formatMac($mac):string
    {
        //Sometimes, MACs are provided in a format where they are colon separated, but missing leading zeroes.
        if (strpos($mac,":") !== false)
        {
            $fixedMac = [];
            $boom = explode(":",$mac);
            foreach ($boom as $shard)
            {
                if (strlen($shard) == 1)
                {
                    $shard = "0" . $shard;
                }
                array_push($fixedMac,$shard);
            }

            $mac = implode(":",$fixedMac);
        }

        $cleanMac = strtoupper(preg_replace("/[^A-Fa-f0-9]/", '', $mac));
        if (strlen($cleanMac) !== 12)
        {
            throw new FormattingException("$mac cannot be converted to a 12 character MAC address.");
        }
        $macSplit = str_split($cleanMac,2);
        return implode(":",$macSplit);
    }
}