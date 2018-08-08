<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\Account;
use App\Models\Trip;
use App\Models\Price;
use App\Transformers\Api\TripTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use JWTAuthException;

class TripController extends ApiBaseController
{
    public function __construct(Request $request){
        parent::__construct($request);
    }    

    public function _getParams(Request $request) {
        $pick_up      = $request->get('pick_up');
        $drop_off     = $request->get('drop_off');
        $pickup_date  = $request->get('pickup_date');
        $vehicle_type = $request->get('vehicle_type');
        $price        = $request->get('price');
        $status       = $request->get('status');
        $trip_id      = $request->get('trip_id');
        return [$pick_up, $drop_off, $pickup_date, $vehicle_type, $price, $status, $trip_id];
    }

    public function create(Request $request) {
        //list($pick_up, $drop_off, $pickup_date, $vehicle_type, $price, $status, $trip_id) = $this->_getParams($request);
        $data = [];
        
        $trip = $request->get('trip');
        if(!$trip) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        $trips        = json_decode($trip);
        $pick_up      = $trips->pick_up;
        $drop_off     = $trips->drop_off;
        $vehicle_type = $trips->vehicle_type;
        $price        = $trips->price;
        
        if(!$pick_up || !$drop_off || !$vehicle_type || !$price) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        $user_id = $this->account->id;
        $dataInsert = [
            'user_id'      => $user_id,
            'pick_up'      => $pick_up,
            'drop_off'     => $drop_off,
            'pickup_latitude' => $trips->pickup_latitude,
            'pickup_longitude' => $trips->pickup_longitude,
            'dropoff_latitude' => $trips->dropoff_latitude,
            'dropoff_longitude' => $trips->dropoff_longitude,
            //'current_latitude' => $trips->pickup_latitude,
            //'current_longitude' => $trips->current_longitude,
            'is_schedule'  => $trips->is_schedule,
            'pickup_date'  => isset($trips->pickup_date) ? $trips->pickup_date : '',
            'payment_type' => $trips->payment_type,
            'payment_status' => isset($trips->payment_status) ? $trips->payment_status : '',
            'vehicle_type' => $vehicle_type,
            'price'        => $price,
            'status'       => TRIP_STATUS_NEW,
            'created_at'   => time()
        ];
        Trip::insertData($dataInsert);

        $this->status  = 'success';
        $this->message = 'trip create successfully';

        next:
        return $this->ResponseData($data);
    }    

    public function update(Request $request) {
        $data = [];
        list($pick_up, $drop_off, $pickup_date, $vehicle_type, $price, $status, $trip_id) = $this->_getParams($request);

        if(!$trip_id || !$status) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        /** @var Trip $trip */
        $trip      = Trip::find($trip_id);
        $driver_id = $trip->driver_id;
        $user_id   = $trip->user_id;
        if($driver_id) {
            if($this->account->id != $user_id || $this->account->id != $driver_id) {
                $this->message = 'You are not permission with this trip';
                $this->http_code = TRIP_NOT_PERMISSION;
                goto next;
            }
        } else {
            if($this->account->id != $user_id) {
                $this->message = 'You are not permission with this trip';
                $this->http_code = TRIP_NOT_PERMISSION;
                goto next;
            }
        }

        $trip->status  = $status;
        if(!$driver_id && $this->account->type == TYPE_DRIVER && $this->account->id != $user_id) {
            $trip->driver_id = $this->account->id;
        }
        $trip->save();

        $this->status  = STATUS_SUCCESS;
        $this->message = 'update status trip successfully';

        next:
        return $this->ResponseData($data);
    }

    public function getNewTrip(Request $request){
        $data = [];

        list($pick_up, $drop_off, $pickup_date, $vehicle_type, $price, $status, $trip_id) = $this->_getParams($request);
        $vehicle_type = $this->account->driverR->vehicle_type;
        $trips = Trip::getNewTrip($vehicle_type);
        if(!empty($trips)) {
            foreach ($trips as $trip) {
                $trip = new TripTransformer($trip);
                $data[] = $trip;
            }
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'get trip successful';
        return $this->ResponseData($data);
    }

    public function getMyTrip(Request $request){
        $data = [];

        list($pick_up, $drop_off, $pickup_date, $vehicle_type, $price, $status, $trip_id) = $this->_getParams($request);
        $account_type = $this->account->type;
        $account_id   = $this->account->id;

        if($account_type == TYPE_USER)
            $trips = Trip::getMyTrip($account_id, false);
        else
            $trips = Trip::getMyTrip(false, $account_id);

        if(!empty($trips)) {
            foreach ($trips as $trip) {
                $trip = new TripTransformer($trip);
                $data[] = $trip;
            }
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'get trip successful';
        return $this->ResponseData($data);
    }
    
    public function getPriceByDistance(Request $request){
        $data = [];
        
        $distance = $request->get('distance');
        if(!$distance) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }
        
        $price = Price::getPriceByDistance($distance);
        if(empty($price)) {
            $this->message = 'Price not found';
            $this->http_code = PRICE_NOT_FOUND;
            goto next;
        }
        
        $price = floatval($price->price);
        $data  = $price;
        
        $this->status  = STATUS_SUCCESS;
        $this->message = 'get price successful';
        
        next:
        return $this->ResponseData($data);
    }
}