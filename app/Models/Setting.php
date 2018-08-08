<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{
    const 
        STATUS_ACTIVE = 1,
        STATUS_INACTIVE = 0;
    
    protected $table = 'settings';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'settingKey', 'settingValue'
    ];
    //public $timestamps = false;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'avatar'
    ];

    public static function getValueByKey($key) {
        $setting = self::query()->where('settingKey', $key)->first();
        return $setting ? $setting->settingValue : '';
    }

    public static function setValueByKey($key, $value) {
        $setting = self::query()->where('settingKey', $key)->first();
        if(!empty($setting)) {
            $setting->settingKey = $value;
            $setting->save();
        }
    }
}
