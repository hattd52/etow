<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;
use Illuminate\Support\Facades\DB;

class TripPaid extends Model
{
    //use SyncsWithFirebase;

    protected $table = 'trip_paid';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trip_id', 'price', 'created_at', 'updated_at'
    ];

    public function tripR() {
        return $this->belongsTo(Trip::class, 'trip_id', 'id');
    }

    public static function insertData($data) {
        DB::transaction(function () use ($data) {
            $trip = new TripPaid();
            foreach ($data as $key => $value) {
                $trip->$key = $value;
            }
            $trip->save();
        });
    }

    public static function updateData($condition, $data) {
        DB::transaction(function () use ($condition, $data) {
            DB::table('trip_paid')->where($condition)->update($data);
        });
    }
}
