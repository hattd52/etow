<?php

namespace App\Models;

use App\Models\Account;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;
use Illuminate\Support\Facades\DB;


class Driver extends Model
{
    use SyncsWithFirebase;
    
    const
        DRIVER_ONLINE  = 'online',
        DRIVER_OFFLINE = 'offline',
        FREE_DRIVER    = 'free',
        DRIVER_ON_TRIP = 'on_trip';
        
    protected $table = 'driver';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'vehicle_type', 'vehicle_number', 'company_name',  'created_at', 'updated_at', 'driver_code', 'driver_license', 'emirate_id', 'mulkiya'
    ];
    public $timestamps = false;

    public function userR()
    {
        return $this->belongsTo(Account::class, 'user_id');
    }

    public function tripR() {
        return $this->hasMany(Trip::class, 'driver_id', 'user_id');
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

    public static function checkPhoneExist($phone) {
        return $users = DB::table('otp')->where('phone', $phone)->first();
    }

    public static function checkOtp($otp) {
        return $users = DB::table('otp')->where('otp', $otp)->first();
    }
    
    public static function insertData($data) {
        DB::transaction(function () use ($data) {
            DB::table('driver')->insert($data);
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
            DB::table('otp')->where($condition)->update($data);
        });
    }

    public static function deleteAccount($condition) {
        DB::transaction(function () use ($condition) {
            DB::table('otp')->where($condition)->delete();
        });
    }
    
    public function getNextDriverCode()
    {
        $curr_code = intval(self::query()->max('driver_code'));
        return str_pad(++$curr_code, 10, 0, STR_PAD_LEFT);
    }

    public function search($params, $order, $column, $offset, $limit, $count) {
        $query = self::query();
        $query->join('account', 'account.id', '=', 'driver.user_id');
        $query->select('driver.*');

        $table = 'driver';
        $query = $this->loadParams($query, $params, $table);

        if ($column) {
            list($query, $column) = $this->getColumn($query, $column);
        }

        if ($order) {
            if(!$column)
                $query->orderBy('driver.id', 'desc');
            else
                $query->orderBy($column, $order);
        }

        if ($count) {
            if($params['type'] && in_array($params['type'], [FREE_DRIVER, DRIVER_ON_TRIP]))
                $user = intval($query->count(DB::raw('DISTINCT(driver.user_id)')));
            else
                $user = intval($query->count());
        } else {
            if ($offset)
                $query->offset($offset);
            if ($limit)
                $query->limit($limit);

            if($params['type'] && in_array($params['type'], [FREE_DRIVER, DRIVER_ON_TRIP])) {
                $query->groupBy('trip.driver_id');
            }
            //dd($query->toSql());
            $user = $query->get();
        }

        return $user;
    }

    public function loadParams($query, $params, $table) {
        if (isset($params['key'])) {
            $query->where(function ($query) use ($params, $table) {
                $table_join = 'account';
                $query->where($table . '.driver_code', 'like', '%' . $params['key'] . '%')
                    ->orWhere($table . '.company_name', 'like', '%' . $params['key'] . '%')
                    ->orWhere($table_join . '.full_name', 'like', '%' . $params['key'] . '%');
            });
        }

        if (isset($params['type'])) {
            $table_join = 'trip';
            $driverOnTrip = [];
            if(in_array($params['type'], [FREE_DRIVER, DRIVER_ON_TRIP])) {
                $driverOnTrip = $this->getListDriverOnTrip();
                $query->leftJoin('trip', 'trip.driver_id', '=', 'driver.user_id');
            }
            
            switch ($params['type']) {
                case DRIVER_ONLINE:
                    $query->where($table.'.is_online', config('main.driver_status.online'));
                    break;
                case DRIVER_OFFLINE:
                    $query->where($table.'.is_online', config('main.driver_status.offline'));
                    break;
                case FREE_DRIVER:
                    $query->where(function ($query) use ($params, $table_join) {
                        $query->whereNull($table_join . '.driver_id');
                    });
                    $query->orWhere(function ($query) use ($params, $table, $table_join, $driverOnTrip) {
                        //$query->whereNotNull($table_join . '.driver_id');
                        //$query->where($table_join . '.driver_id', '=', $table.'.user_id');
                        $query->whereIn($table_join . '.status', [TRIP_STATUS_CANCEL, TRIP_STATUS_REJECT, TRIP_STATUS_COMPLETED]);
                        if(!empty($driverOnTrip)) {
                            $query->whereNotIn($table_join . '.driver_id', $driverOnTrip);
                        }
                    });
                    break;
                case DRIVER_ON_TRIP:
                    $query->whereIn($table_join.'.status', [TRIP_STATUS_ACCEPT, TRIP_STATUS_ARRIVED, TRIP_STATUS_JOURNEY_COMPLETED, TRIP_STATUS_ON_GOING]);
                    break;
            }            
        }

        return $query;
    }

    public function getColumn($query, $column) {
        switch ($column) {
            case 0:
                $column = 'account.id';
                break;
            case 1:
                $column = 'driver.driver_code';
                break;
            case 2:
                $column = 'account.full_name';
                break;
        }

        return [$query, $column];
    }

    public function getListDriverOnTrip() {
        return Trip::query()->whereNotIn('status', [TRIP_STATUS_CANCEL, TRIP_STATUS_REJECT, TRIP_STATUS_COMPLETED])
            ->whereNotNull('driver_id')
            ->groupBy('driver_id')
            ->pluck('driver_id');
    }

    public function getTotalDriver() {
        return Driver::query()->count();
    }

    public function getTotalDriverFree() {
        $driverOnTrips = $this->getListDriverOnTrip();
        $query = self::query();
        $query->leftJoin('trip', 'trip.driver_id', '=', 'driver.user_id');
        $query->where(function ($query) {
            $query->whereNull('trip.driver_id');
        });
        $query->orWhere(function ($query) use ($driverOnTrips) {
            $query->where('trip.status', TRIP_STATUS_COMPLETED);
            $query->whereNotIn('trip.driver_id', $driverOnTrips);
        });
        return $query->count();
    }

    public function getTotalDriverOffline() {
        return Driver::query()->where('is_online', STATUS_INACTIVE)->count();
    }

    public static function getListDriverOnMap() {
        $query = Driver::query();
        $query->join('account', 'account.id', '=', 'driver.user_id');
        $query->where('account.status', STATUS_ACTIVE);
        return $query->get();
    }

    public static function getCurrentTrip($driver_id) {
        return Trip::query()->where('driver_id', $driver_id)
            ->latest()
            ->first();
    }
}
