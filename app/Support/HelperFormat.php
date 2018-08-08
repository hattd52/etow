<?php

namespace App\Support;

use Carbon\Carbon;

class HelperFormat 
{
    /**
     * Format date to sql Y-m-d format
     */
    public static function dateToSqlDate($date)
    {
        if (!$date) return null;

        return date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    }

    /**
     * Format date using Carbon
     */
    public static function date($date, $format = "d/m/Y H:i:s")
    {
        if (!$date) return null;
        
        //Set Carbon datetime locale 
        self::setCarbonLocale();

        return (new Carbon($date))->format($format);
    }

    /**
     * Helper to set Carbon localization using Locale setting from database
     */
    public static function setCarbonLocale()
    {
        //Set Carbon datetime locale 
        $appLocale = locale();
        
        Carbon::setLocale($appLocale ? $appLocale : 'vi');
        Carbon::setUtf8(true);
    }

}