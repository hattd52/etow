<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;
use Illuminate\Support\Facades\DB;

class TripReject extends Model
{
    //use SyncsWithFirebase;

    protected $table = 'trip_reject';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trip_id', 'driver_id', 'note', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        //
    ];

    public function tripR() {
        return $this->belongsTo(Trip::class, 'trip_id', 'id');
    }

    public static function insertData($data) {
        DB::transaction(function () use ($data) {
            $trip = new TripReject();
            foreach ($data as $key => $value) {
                $trip->$key = $value;
            }
            $trip->save();
        });
    }

    public static function updateData($condition, $data) {
        DB::transaction(function () use ($condition, $data) {
            DB::table('trip_reject')->where($condition)->update($data);
        });
    }

    public static function deleteAccount($condition) {
        DB::transaction(function () use ($condition) {
            DB::table('trip_reject')->where($condition)->delete();
        });
    }
}
