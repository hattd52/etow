<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Otp extends Model
{
    const 
        STATUS_ACTIVE = 1,
        STATUS_INACTIVE = 0;
    
    protected $table = 'otp';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone', 'otp', 'created_at', 'updated_at'
    ];
    public $timestamps = false;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'avatar'
    ];

    public static function checkPhoneExist($phone) {
        return $users = DB::table('otp')->where('phone', $phone)->first();
    }

    public static function checkOtp($otp) {
        return $users = DB::table('otp')->where('otp', $otp)->first();
    }

    public static function updateData($condition, $data) {
        DB::transaction(function () use ($condition, $data) {
            DB::table('otp')->where($condition)->update($data);
        });
    }

    public static function deleteAccount($condition) {
        DB::transaction(function () use ($condition) {
            DB::table('otp')->where($condition)->delete();
        });
    }
}
