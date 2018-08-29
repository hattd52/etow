<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;
use Illuminate\Support\Facades\DB;

class Trip extends Model
{
    use SyncsWithFirebase;

    protected $table = 'trip';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'driver_id', 'pick_up', 'drop_off', 'pickup_date', 'vehicle_type', 'price', 'status',
        'created_at', 'updated_at', 'is_schedule', 'note', 'pickup_latitude', 'pickup_longitude', 'dropoff_latitude',
        'dropoff_longitude', 'payment_type', 'current_latitude', 'current_longitude', 'payment_status', 'rate', 'is_settlement'
    ];
    
    protected $hidden = [
        'userR', 'driverR'
    ];

    public function userR() {
        return $this->belongsTo(Account::class, 'user_id', 'id');
    }

    public function driverR() {
        return $this->belongsTo(Driver::class, 'driver_id', 'user_id');
    }
    
    public function tripPaidR() {
        return $this->belongsTo(TripPaid::class, 'id', 'trip_id');
    }    
    
    protected $appends = ['user', 'driver', 'status_schedule'];
        
    public function getUserAttribute()
    {
        return $this->userR ? $this->userR : '';
    }

    public function getDriverAttribute()
    {
        return $this->driverR ? $this->driverR : (object)(["name" => "Yoona"]);
    }
    
    public function getStatusScheduleAttribute()
    {
        return $this->status.'_'.$this->is_schedule;
    }
    
    public static function totalTripByUserAndStatus($uid, $status) {
        $query = self::query();
        $query->where('user_id', $uid);
        if(in_array(TRIP_STATUS_NEW, $status))
           $query->where('is_schedule', STATUS_ACTIVE); 
        else 
            $query->whereIn('status', $status);
        
        return $query->count();
    }

    public static function totalTripByDriverAndStatus($driver_id, $status) {
        $query = self::query()->where('driver_id', $driver_id);
        if(in_array(TRIP_STATUS_NEW, $status))
            $query->where('is_schedule', STATUS_ACTIVE);
        else
            $query->whereIn('status', $status);

        return $query->count();
    }

    public static function totalTripByStatus($status) {
        return self::query()->whereIn('status', $status)->count();
    }  

    public static function insertData($data) {
        DB::transaction(function () use ($data) {
            $trip = new Trip();
            foreach ($data as $key => $value) {
                $trip->$key = $value;
            }
            $trip->save();
        });
    }

    public static function updateData($condition, $data) {
        DB::transaction(function () use ($condition, $data) {
            DB::table('trip')->where($condition)->update($data);
        });
    }

    public static function deleteAccount($condition) {
        DB::transaction(function () use ($condition) {
            DB::table('trip')->where($condition)->delete();
        });
    }
    
    public static function getNewTrip($vehicle_type) {
        $query = Trip::where('vehicle_type', $vehicle_type)
            ->where('status', TRIP_STATUS_NEW)
            ->whereNull('driver_id');
        return $query->get();
    }

    public static function getMyTrip($user_id = false, $driver_id = false) {
        if($user_id)
            $trip = Trip::where('user_id', $user_id)->get();
        else
            $trip = Trip::where('driver_id', $driver_id)->get();
        return $trip;
    }
    
    public function search($params, $order, $column, $offset, $limit, $count) {
        $query = self::query();
        $query->join('account', 'account.id', '=', 'trip.user_id');
        $query->leftJoin('driver', 'driver.user_id', '=', 'trip.driver_id');
        $query->select('trip.*');

        $table = 'trip';
        $query = $this->loadParams($query, $params, $table);

        if (isset($column)) {
            list($query, $column) = $this->getColumn($query, $column);
        }

        if ($order) {
            if(!$column)
                $query->orderBy('trip.id', 'desc');
            else
                $query->orderBy($column, $order);
        }

        if ($count) {
            $user = intval($query->count());
        } else {
            if ($offset)
                $query->offset($offset);
            if ($limit)
                $query->limit($limit);
            //dd($query->toSql());
            $user = $query->get();
        }

        return $user;
    }

    public function loadParams($query, $params, $table) {
        if (isset($params['key'])) {
            $query->where(function ($query) use ($params, $table) {
                $table_join  = 'account';
                $table_join1 = 'driver';
                $query->where($table . '.id', 'like', '%' . $params['key'] . '%')
                    ->orWhere($table_join1 . '.company_name', 'like', '%' . $params['key'] . '%')
                    ->orWhere($table_join . '.full_name', 'like', '%' . $params['key'] . '%');
            });
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $start_date = date('Y-m-d 00:00:00', strtotime($params['start_date']));
            $end_date   = date('Y-m-d 23:59:59', strtotime($params['end_date']));
            $query->whereBetween($table.'.created_at', [$start_date, $end_date]);
        } else {
            if (isset($params['start_date'])) {
                $start_date = date('Y-m-d 00:00:00', strtotime($params['start_date']));
                $query->where($table.'.created_at', '>=', $start_date);
            }

            if (isset($params['end_date'])) {
                $end_date = date('Y-m-d 23:59:59', strtotime($params['end_date']));
                $query->where($table.'.created_at', '<=', $end_date);
            }
        }

        if (isset($params['type'])) {            
            switch ($params['type']) {
                case TRIP_ON_GOING:
                    $query->whereIn($table.'.status', [TRIP_STATUS_ACCEPT, TRIP_STATUS_ARRIVED, TRIP_STATUS_JOURNEY_COMPLETED, TRIP_STATUS_ON_GOING]);
                    break;
                case TRIP_SCHEDULE:
                    $query->where(function ($query) use ($params, $table) {
                        $query->where($table . '.is_schedule', STATUS_ACTIVE)
                            ->Where($table . '.status', TRIP_STATUS_NEW);
                    });
                    break;
                case TRIP_COMPLETE:
                    $query->where($table.'.status', TRIP_STATUS_COMPLETED);
                    break;
                case TRIP_REJECT:
                    $query->where($table . '.status', TRIP_STATUS_REJECT);
                    break;
                case TRIP_CANCEL:
                    $query->where($table . '.status', TRIP_STATUS_CANCEL);
                    break;
            }
        }
        if (isset($params['user_id'])) {
            $query->where('trip.user_id', $params['user_id']);
        }
        if (isset($params['driver_id'])) {
            $query->where('trip.driver_id', $params['driver_id']);
            if(isset($params['payment_driver'])) {
                $paymentDriver = $params['payment_driver'];
                if($paymentDriver === PAYMENT_DRIVER_PENDING)
                    $query->where('trip.is_settlement', UNPAID);
                else
                    $query->where('trip.is_settlement', PAID);
            }
        }

        return $query;
    }

    public function getColumn($query, $column) {
        switch ($column) {
            case 0:
                $column = 'trip.id';
                break;
            case 1:
                $column = 'trip.created_at';
                break;
        }

        return [$query, $column];
    }

    public function getTotalTrip() {
        return self::query()->count();
    }

    public function getTripGoing() {
        $status = [TRIP_STATUS_ACCEPT, TRIP_STATUS_ARRIVED, TRIP_STATUS_ON_GOING, TRIP_STATUS_JOURNEY_COMPLETED];
        return self::query()
            ->whereIn('status', $status)
            ->whereNotNull('current_latitude')
            ->whereNotNull('current_longitude')
            ->get();
    }
}
