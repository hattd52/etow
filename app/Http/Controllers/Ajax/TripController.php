<?php
/**
 * Created by PhpStorm.
 * User: tienvm
 * Date: 12/21/17
 * Time: 10:36 AM
 */

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\ApiBaseController;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Driver;
use App\Models\Otp;
use App\Models\Trip;
use App\Models\UserToken;
use App\Transformers\Api\AccountTransformer;
use App\Transformers\Api\DriverTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Exceptions\JWTException;
use \Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    private $driver;
    private $account;
    private $trip;
    public function __construct(Account $account, Driver $driver, Trip $trip){
        $this->account = $account;
        $this->driver = $driver;
        $this->trip = $trip;
    }

    public function search(Request $request) {
        $key        = $request->input('key');
        $type       = $request->input('type');
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');
        $user_id    = $request->input('user_id');

        $orderName = $request->input('order');
        if ($orderName) {
            $order = $orderName[0]['dir'];
            $column = $orderName[0]['column'];
        } else {
            $order = null;
            $column = null;
        }

        $params = compact('key', 'type', 'start_date', 'end_date', 'user_id');
        $offset = $request->input('start');
        $limit  = $request->input('length');
        $data   = [];
        $total  = 0;

        $trips = $this->trip->search($params, $order, $column, $offset, $limit, false);
        if($trips->isEmpty() == true){
            return $this->_getResponse($request, $total, $data);
        }

        $i = $request->input('start');
        foreach ($trips as $trip) {
            $i++;
            $tmp = [
                $i,
                date('d-m-Y H:i A', strtotime($trip->created_at)),
                '#'.$trip->id,
                $trip->driverR ? $trip->driverR->driver_code : '',
                ($trip->driverR && $trip->driverR->userR) ? $trip->driverR->userR->full_name : '',
                ($trip->driverR && $trip->driverR->userR) ? $trip->driverR->userR->phone : '',
                $trip->driverR ? $trip->driverR->vehicle_number : '',
                $trip->driverR ? $trip->driverR->company_name : '',
                $trip->userR ? $trip->userR->full_name : '',
                $trip->userR ? $trip->userR->phone : '',
                $trip->pick_up,
                $trip->drop_off,
                $trip->price.' AED',
                $trip->is_schedule == STATUS_ACTIVE ? '<span style="color: orange">Schedule</span>' : '<span>Normal</span>',
                $trip->pickup_date,
                $this->getLabelStatus($trip->status, $trip->is_schedule),
                $trip->note.'', // reason for cancel/reject
                '', // paid by cash
                '', // paid by card
                '', // payment status
                "<img src='assets/img/rating.png'  alt='' />",
            ];
            $data[] = $tmp;
        }

        $total = $this->trip->search($params, $order, $column, $offset, $limit, true);
        return $this->_getResponse($request, $total, $data);
    }

    public function _getResponse($request, $total, $data) {
        return response()->json([
            'draw' => $request->input('draw'),
            "recordsTotal" => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    public function getLabelStatus($status, $schedule) {
        switch ($status) {
            case TRIP_STATUS_NEW:
                return $schedule ? '<span class="label label-warning">Schedule</span>' :
                    '<span class="label label-default">Normal</span>';
                break;
            case TRIP_STATUS_CANCEL:
                return '<span class="label label-danger" style="background: #c40505 !important;">Canceled</span>' ;
                break;
            case TRIP_STATUS_REJECT:
                return '<span class="label label-danger" style="background: #ff0000 !important;">Canceled</span>' ;
                break;
            case TRIP_STATUS_ACCEPT:
                return '<span class="label label-info" style="">Accept</span>' ;
                break;
            case TRIP_STATUS_ARRIVED:
                return '<span class="label label-primary" style="">Arrived</span>' ;
                break;
            case TRIP_STATUS_JOURNEY_COMPLETED:
                return '<span class="label label-primary" style="">Journey Completed</span>' ;
                break;
            case TRIP_STATUS_COMPLETED:
                return '<span class="label label-success" style="">Completed</span>' ;
                break;
        }
    }
}