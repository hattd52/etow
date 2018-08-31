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
use App\Models\TripPaid;
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
use FarhanWazir\GoogleMaps\GMaps;

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
        $driver_id  = $request->input('driver_id');
        $payment_driver = $request->input('payment_driver');

        $orderName = $request->input('order');
        if ($orderName) {
            $order = $orderName[0]['dir'];
            $column = $orderName[0]['column'];
        } else {
            $order = null;
            $column = null;
        }

        $params = compact('key', 'type', 'start_date', 'end_date', 'user_id', 'driver_id', 'payment_driver');
        $offset = $request->input('start');
        $limit  = $request->input('length');
        $data   = [];
        $total  = 0;

        $trips = $this->trip->search($params, $order, $column, $offset, $limit, false);
        $total_cash = $total_card = $total_paid_cash = $total_paid_card = $total_pay = $total_collect = 0;
        if($trips->isEmpty() == true){
            return $this->_getResponse($request, $total, $data, $total_cash, $total_card, $total_paid_cash, $total_paid_card,
                $total_pay, $total_collect);
        }

        $i = $request->input('start');
        foreach ($trips as $trip) {
            $i++;
            $tmp = $this->_getTmpComplete($i, $trip);
            if($payment_driver && $payment_driver === PAYMENT_DRIVER_PAID) {
                $settlement = '<p class="label label-success">Paid</p>
                    <p>'.date('H:i A, d,m,Y',strtotime($trip->tripPaidR->created_at)).'</p>';
            } else {
                $settlement = '<a id="btnPaid" class="btn btn-success" onclick="paidTrip('.$trip->id.')">Paid</a>';
            }
            array_push($tmp, $settlement);
            $data[] = $tmp;

            if($trip->payment_method === PAYMENT_METHOD_CASH) {
                $total_cash += $trip->price;
                if($trip->is_settlement) {
                    $total_paid_cash += $trip->price;
                }
            } else {
                $total_card += $trip->price;
                if($trip->is_settlement) {
                    $total_paid_card += $trip->price;
                }
            }
        }
        
        $total_pay     = (($total_cash + $total_card) - ($total_paid_cash + $total_paid_card));
        $total_collect = $total_paid_cash + $total_paid_card;
        $total = $this->trip->search($params, $order, $column, $offset, $limit, true);

        $params['type'] = '';
        $totalAll = $this->trip->search($params, $order, $column, $offset, $limit, true);
        $params['type'] = TRIP_ON_GOING;
        $totalOnGoing = $this->trip->search($params, $order, $column, $offset, $limit, true);
        $params['type'] = TRIP_SCHEDULE;
        $totalSchedule = $this->trip->search($params, $order, $column, $offset, $limit, true);
        $params['type'] = TRIP_COMPLETE;
        $totalComplete = $this->trip->search($params, $order, $column, $offset, $limit, true);
        $params['type'] = TRIP_REJECT;
        $totalReject = $this->trip->search($params, $order, $column, $offset, $limit, true);
        $params['type'] = TRIP_CANCEL;
        $totalCancel = $this->trip->search($params, $order, $column, $offset, $limit, true);
        return $this->_getResponse($request, $total, $data, $total_cash, $total_card, $total_paid_cash, $total_paid_card,
            $total_pay, $total_collect, $totalAll, $totalOnGoing, $totalSchedule, $totalComplete, $totalReject, $totalCancel);
    }

    public function _getTmpNormal($i, $trip) {
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
        ];
        return $tmp;
    }

    public function _getTmpComplete($i, $trip){
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
            $trip->payment_status == PAYMENT_STATUS_PENDING ? '' :
                ($trip->payment_status == PAYMENT_STATUS_SUCCESS ? $trip->price.' AED' : ''), // paid by cash
            '', // paid by card
            $trip->payment_status == PAYMENT_STATUS_PENDING ? '<span style="color:red">Pending</span>' :
                ($trip->payment_status == PAYMENT_STATUS_SUCCESS ?
                    '<span style="color:green">Paid</span>' : '<span style="color:red">Failed</span>'), // payment status
            //"<img src='assets/img/rating.png'  alt='' />",
            '<div class="rating">'.
                $this->stars($trip->rate/2).
            '</div>'
        ];
        return $tmp;
    }

    public function _getResponse($request, $total, $data, $total_cash, $total_card, $total_paid_cash, $total_paid_card,
        $total_pay, $total_collect, $totalAll = false, $totalOnGoing = false, $totalSchedule = false,
             $totalComplete = false, $totalReject = false, $totalCancel = false) {
        return response()->json([
            'draw' => $request->input('draw'),
            "recordsTotal" => $total,
            'recordsFiltered' => $total,
            'data' => $data,
            'total_cash' => $total_cash,
            'total_card' => $total_card,
            'total_paid_cash' => $total_paid_cash,
            'total_paid_card' => $total_paid_card,
            'total_pay' => $total_pay,
            'total_collect' => $total_collect,
            'total_all' => $totalAll,
            'total_onGoing' => $totalOnGoing,
            'total_schedule' => $totalSchedule,
            'total_complete' => $totalComplete,
            'total_reject' => $totalReject,
            'total_cancel' => $totalCancel
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
                return '<span class="label label-danger" style="background: #ff0000 !important;">Rejected</span>' ;
                break;
            case TRIP_STATUS_ACCEPT:
                return '<span class="label label-info" style="">Accept</span>' ;
                break;
            case TRIP_STATUS_ARRIVED:
                return '<span class="label label-info" style="">Arrived</span>' ;
                break;
            case TRIP_STATUS_ON_GOING:
                return '<span class="label label-primary" style="">Ongoing</span>' ;
                break;
            case TRIP_STATUS_JOURNEY_COMPLETED:
                return '<span class="label label-primary" style="">Journey Completed</span>' ;
                break;
            case TRIP_STATUS_COMPLETED:
                return '<span class="label label-success" style="">Completed</span>' ;
                break;
        }
    }

    public function map()
    {
        $config['center'] = 'Sydney Airport,Sydney';
        $config['zoom'] = '14';
        $config['map_height'] = '400px';

        $gmap = new GMaps();
        $gmap->initialize($config);

        $marker['position'] = 'Sydney Airport,Sydney';
        $marker['infowindow_content'] = 'Sydney Airport,Sydney';
        $gmap->add_marker($marker);

        $marker['position'] = 'Kogarah Golf Club,Sydney';
        $marker['infowindow_content'] = 'Kogarah Golf Club,Sydney';
        $gmap->add_marker($marker);

        $marker['position'] = 'The Lakes Golf Club,Sydney';
        $marker['infowindow_content'] = 'The Lakes Golf Club,Sydney';
        $gmap->add_marker($marker);

        $map = $gmap->create_map();
        
        return '<div class="container">'.
        '{!! $map["html"] !!}'.
               '</div>';
    }
    
    public function getMarker() {
        $data = [];
        $trip_going = $this->trip->getTripGoing();
        if(!empty($trip_going)) {
            foreach ($trip_going as $item) {
                $data[] = [
                    'coords' => ['lat' => floatval($item->current_latitude), 'lng' => floatval($item->current_longitude)],
                    'iconImage' => $item->driverR->is_online == STATUS_ACTIVE ? asset('assets/img/car_online.png') :
                        asset('assets/img/car_offline.png'),
                    'content' =>
                        '<div style="background: black; color: white">    
                            <ul>
                                <li style="list-style: none">Type: '.$item->vehicle_type.'</li>
                                <li style="list-style: none">Plate No: '.($item->driverR ? $item->driverR->vehicle_number : '').'</li>
                                <li style="list-style: none">Driver ID: '.($item->driverR ? $item->driverR->driver_code : '').'</li>
                                <li style="list-style: none">Driver Name: '.(($item->driverR && $item->driverR->userR) ? $item->driverR->userR->full_name : '').'<li/>
                                <li style="list-style: none">Phone: '.(($item->driverR && $item->driverR->userR) ? $item->driverR->userR->phone : '').'</li>
                            </ul>
                        </div>'
                ];
            }
        }
        
        return json_encode($data);
    }

    public function paidTrip(Request $request) {
        $trip_id = $request->get('trip_id');
        if(!$trip_id)
            return 0;

        $trip = Trip::find($trip_id);
        if(empty($trip))
            return 0;

        $dataPaid = [
            'trip_id' => $trip_id,
            'price'   => $trip->price,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time())           
        ];
        TripPaid::insertData($dataPaid);
        Trip::updateData(['id' => $trip_id],['is_settlement' => PAID]);

        return 1;
    }
}