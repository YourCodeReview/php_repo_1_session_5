<?php

namespace App\Entity\Cast;

use CodeIgniter\Entity\Cast\BaseCast;

class DateDateTimeSec extends BaseCast
{
    public static function get($value, array $params = [])
    {
        if (is_null($value)) {
            return null;
        }
        return date('d.m.Y H:i:s', strtotime($value));
    }

    public static function set($value, array $params = [])
    {
        if (is_null($value)) {
            return null;
        }
        return date('Y-m-dTH:i:sZ', strtotime($value));
    }
}
