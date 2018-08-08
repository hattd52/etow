<?php

namespace App\Support;

use Carbon\Carbon;

class HelperData
{
    public static function attachMeta(&$collection, $metaData)
    {
        $collection->meta = $metaData;
    }

    public static function getMeta($collection)
    {
        return isset($collection->meta) ? $collection->meta : null;
    }
}