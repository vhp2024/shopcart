<?php
namespace ZacoSoft\ZacoBase\Libraries\Common;

use Carbon\Carbon;

class Date
{
    public static function now()
    {
        return Carbon::now();
    }

    public static function code6($format = 'ymd')
    {
        return Carbon::now()->format($format);
    }

    public function formatTimestamp($timestamp, $format = 'Y/m/d H:i:s')
    {
        return Carbon::createFromTimestamp($timestamp)->format($format);
    }
}
