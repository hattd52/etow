<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;
use Illuminate\Support\Facades\DB;


class Device extends Model
{
    protected $table = 'device';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'ime', 'token', 'status',  'created_at', 'updated_at'
    ];
    public $timestamps = false;

    public function userR()
    {
        return $this->belongsTo(Account::class, 'user_id');
    }

    public static function avgRate($driver_id) {
        return DB::table('trip')->where('driver_id', $driver_id)->avg('rate');
    }
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'avatar'
    ];
    
    public static function insertData($data) {
        DB::transaction(function () use ($data) {
            DB::table('device')->insert($data);
            //self::create($data);
//            $account = new Driver();
//            foreach ($data as $key => $value) {
//                $account->$key = $value;
//            }
//            $account->save();
        });
    }

    public static function updateData($condition, $data) {
        DB::transaction(function () use ($condition, $data) {
            DB::table('device')->where($condition)->update($data);
        });
    }

    public static function deleteAccount($condition) {
        DB::transaction(function () use ($condition) {
            DB::table('device')->where($condition)->delete();
        });
    }

    public static function checkUserExist($user_id) {
        return Device::query()->where([
            ['user_id', $user_id],
            ['status', STATUS_ACTIVE]
        ])->first();
    }

    public static function getTokenByUser($user_id) {
        return Device::query()->where([
            ['user_id', $user_id],
            ['status', STATUS_ACTIVE]
        ])->pluck('token');
    }
}
