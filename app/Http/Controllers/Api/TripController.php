<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\Account;
use App\Models\Trip;
use App\Models\TripReject;
use App\Models\Price;
use App\Transformers\Api\TripTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use JWTAuthException;
use App\Models\Setting;
use App\Models\Device;

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

        $distance     = isset($trips->distance) ? $trips->distance : '';
        if($distance) {
            $priceAmount = $this->getPriceDistance($distance);
            if($trips->vehicle_type === VEHICLE_TYPE_FLAT_BED) {
                $priceAmount += 100;
            }
            if($priceAmount !=  $price) {
                $this->message = 'Price invalid. Please check again.';
                $this->http_code = MISSING_PARAMS;
                goto next;
            }
        }

        if(!$pick_up || !$drop_off || !$vehicle_type || !$price) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        $user_id = $this->account->id;
        $trip_id = $this->_createTrip($user_id, $trips);
        //Trip::insertData($dataInsert);

        if($trip_id) {
            $data['trip_id'] = $trip_id;
            $this->status  = 'success';
            $this->message = 'trip create successfully';
        } else {
            $this->message = 'trip create failed.';
        }

        next:
        return $this->ResponseData($data);
    }

    public function _createTrip($user_id, $trips) {
        $trip_id = '';
        $tripNew                    = new Trip();
        $tripNew->user_id           = $user_id;
        $tripNew->pick_up           = $trips->pick_up;
        $tripNew->drop_off          = $trips->drop_off;
        $tripNew->pickup_latitude   = $trips->pickup_latitude;
        $tripNew->pickup_longitude  = $trips->pickup_longitude;
        $tripNew->dropoff_latitude  = $trips->dropoff_latitude;
        $tripNew->dropoff_longitude = $trips->dropoff_longitude;
        $tripNew->is_schedule       = $trips->is_schedule;
        $tripNew->pickup_date       = isset($trips->pickup_date) ? $trips->pickup_date : '';
        $tripNew->payment_type      = $trips->payment_type;
        $tripNew->payment_status    = isset($trips->payment_status) ? $trips->payment_status : PAYMENT_STATUS_PENDING;
        $tripNew->vehicle_type      = $trips->vehicle_type;
        $tripNew->price             = $trips->price;
        $tripNew->status            = TRIP_STATUS_NEW;
        $tripNew->created_at        = time();
        if($tripNew->save()) {
            $trip_id = $tripNew->id;
        }
        return $trip_id;
    }

    public function update(Request $request) {
        $data = [];
        list($pick_up, $drop_off, $pickup_date, $vehicle_type, $price, $status, $trip_id) = $this->_getParams($request);
        $note = $request->get('note');

        if(!$trip_id || !$status) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        if(in_array($status, [TRIP_STATUS_CANCEL, TRIP_STATUS_REJECT]) && !$note) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        /** @var Trip $trip */
        $trip      = Trip::find($trip_id);
        $driver_id = $trip->driver_id;
        $user_id   = $trip->user_id;
        $currentStatus = $trip->status;
        if($driver_id) {
            if($this->account->id != $user_id && $this->account->id != $driver_id) {
                $this->message = 'You are not permission with this trip';
                $this->http_code = TRIP_NOT_PERMISSION;
                goto next;
            }
        } else {
            if($this->account->type == 1 && $this->account->id != $user_id) { // user other
                $this->message = 'You are not permission with this trip';
                $this->http_code = TRIP_NOT_PERMISSION;
                goto next;
            }
        }

        if(in_array($currentStatus, [TRIP_STATUS_CANCEL, TRIP_STATUS_COMPLETED])) {
            $this->message = 'Trip canceled or completed.';
            $this->http_code = TRIP_CANCEL_OR_COMPLETE;
            goto next;
        }

        if($status != TRIP_STATUS_REJECT) {
            $trip->status  = $status;
        } else { // reject trip
            $this->_insertTripReject($trip_id, $this->account->id, $note);
        }

        if(!$driver_id && $this->account->type == TYPE_DRIVER && $this->account->id != $user_id && $status != TRIP_STATUS_REJECT) {
            $trip->driver_id = $this->account->id;
        }
        if($status == TRIP_STATUS_COMPLETED) {
            $trip->payment_status = PAYMENT_STATUS_SUCCESS;
        } elseif(in_array($status, [TRIP_STATUS_CANCEL, TRIP_STATUS_REJECT])) {
            $trip->payment_status = PAYMENT_STATUS_FAIL;
        }

        $is_save = $trip->save();
//        if($is_save) {
//            if($trip->is_schedule == STATUS_ACTIVE && $trip->status == TRIP_STATUS_ACCEPT) {
//                $this->pushNotifyForUser($trip->user_id);
//            }
//        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'update status trip successfully';

        next:
        return $this->ResponseData($data);
    }

    public function pushNotifyForUser($user_id) {
        $tokenByUser = Device::getTokenByUser($user_id);
        if(!empty($tokenByUser)) {
            $msg = '';
        }
    }

    public function _insertTripReject($trip_id, $driver_id, $note) {
        $rejectTrip = [
            'trip_id' => $trip_id,
            'driver_id' => $driver_id,
            'note' => $note,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];
        TripReject::insertData($rejectTrip);
    }

    public function updateLocation(Request $request) {
        $data = [];
        list($pick_up, $drop_off, $pickup_date, $vehicle_type, $price, $status, $trip_id) = $this->_getParams($request);
        $lat  = $request->get('current_latitude');
        $long = $request->get('current_longitude');
        if(!$trip_id || !$lat || !$long) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        /** @var Trip $trip */
        $trip      = Trip::find($trip_id);
        if(!empty($trip)) {
            $trip->current_latitude  = $lat;
            $trip->current_longitude = $long;
            $trip->save();
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'update location trip successfully';

        next:
        return $this->ResponseData($data);
    }

    public function updatePaymentStatus(Request $request) {
        $data = [];
        list($pick_up, $drop_off, $pickup_date, $vehicle_type, $price, $status, $trip_id) = $this->_getParams($request);
        $payment_status = $request->get('payment_status');
        $payment_type   = $request->get('payment_type');
        if(!$trip_id || !$payment_status) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        /** @var Trip $trip */
        $trip      = Trip::find($trip_id);
        if(!empty($trip)) {
            $trip->payment_type  = $payment_type;
            $trip->payment_status  = $payment_status;
            $trip->save();
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'update location trip successfully';

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
        //dd(intval($distance));
        $price = Price::getPriceByDistance(intval($distance));
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

    public function getSettingTime() {
        $timeKm     = Setting::getValueByKey(SETTING_TIME_KM);
        $timeBuffer = Setting::getValueByKey(SETTING_TIME_BUFFER);
        $timeRequestSchedule = Setting::getValueByKey(SETTING_TIME_REQUEST_SCHEDULE);
        $timeEstimateArrive = Setting::getValueByKey(SETTING_TIME_ESTIMATE_ARRIVE);
        $radiusRequest = Setting::getValueByKey(SETTING_RADIUS_REQUEST);
        $priceFlatbed = Setting::getValueByKey(SETTING_PRICE_FLATBED);

        $data = [
            'time_km'     => $timeKm,
            'time_buffer' => $timeBuffer,
            'time_request_schedule' => $timeRequestSchedule,
            'time_estimate_arrive' => $timeEstimateArrive,
            'radius_request' => $radiusRequest,
            'price_flatbed' => $priceFlatbed
        ];
        $this->status  = STATUS_SUCCESS;
        $this->message = 'Get time setting successful';
        return $this->ResponseData($data);
    }

    public function getPriceDistance($distance) {
        $price = Price::getPriceByDistance($distance);
        if(empty($price)) {
            $price = 0;
        }

        $price = floatval($price->price) * $distance;
        return $price;
    }

    public function rejectTrip() {
        $trips = Trip::getTripReject();
        //dd($trips);
        if(!empty($trips)) {
            foreach ($trips as $trip) {
                //$trip->status = TRIP_STATUS_REJECT;
                //$trip->save();
                //Trip::updateData(['id' => $trip->id], ['status' => TRIP_STATUS_REJECT]);

                /** @var Trip $trip */
                $trip->status = TRIP_STATUS_REJECT;
                $trip->save();
            }
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'Reject trip successful';
        return $this->ResponseData();
    }

    public function getDistanceByCoordinate(Request $request) {
        $trip_lat  = floatval($request->get('trip_lat'));
        $trip_long = floatval($request->get('trip_long'));
        //dd($trip_lat, $trip_long);
        $distances = [];
        $drivers   = Account::query()
            ->selectRaw(DB::raw("(6371 * acos(cos(radians(".$trip_lat."))
                * cos(radians(latitude))
                * cos(radians(longitude)
                - radians(".$trip_long."))
                + sin(radians(".$trip_lat."))
                * sin(radians(longitude)))) as distance, id" ))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('type', TYPE_DRIVER)
            ->get();
        //dd($drivers);
        if(!empty($drivers)) {
            foreach ($drivers as $driver) {
                //$distance =
                dd($driver->distance);
                $distances[$driver->id] = $driver->distance;
            }
        }

        return $this->ResponseData($distances);
    }

    public function pushNotification(Request $request) {
        $user_id = $request->get('user_id');
        $message = $request->get('msg');

        $query = Device::query();
        if($user_id) {
            $query->where('user_id', $user_id);
        }
        $devices = $query->pluck('token');
        if(!empty($devices)) {
            $this->pushNotify($devices, $message);
        }

//        $this->status  = STATUS_SUCCESS;
//        $this->message = 'Push Successfully!';
//        return $this->ResponseData();
    }
}