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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Exceptions\JWTException;
use \Illuminate\Support\Facades\Auth;

class AccountController extends Controller
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
        $key           = $request->input('key');

        $orderName = $request->input('order');
        if ($orderName) {
            $order = $orderName[0]['dir'];
            $column = $orderName[0]['column'];
        } else {
            $order = null;
            $column = null;
        }

        $params = compact('department_id', 'key');
        $offset = $request->input('start');
        $limit  = $request->input('length');
        $data   = [];
        $total  = 0;

        $accounts    = $this->account->search($params, $order, $column, $offset, $limit, false);
        if($accounts->isEmpty() == true){
            return $this->_getResponse($request, $total, $data);
        }

        $i = $request->input('start');
        foreach ($accounts as $account){
            $status_label  = $account->status == 1 ? 'Activate' : 'Deactivate';
            $status_class  = $account->status == 0 ? 'btn-danger' : 'btn-success';
            $status_option_label = $account->status == 0 ? 'Activate' : 'Deactivate';
            $status_option_value = $account->status == 0 ? STATUS_ACTIVE : STATUS_INACTIVE;

            $i++;
            $tmp = [
                $i,
                $account->full_name,
                $account->email,
                $account->phone,
                '<a href="'.route('trip.by_user_type',[$account->id, TRIP_COMPLETE]).'" target="_blank" class="status_green status_txtsize">'.
                    $this->trip->totalTripByUserAndStatus($account->id, [TRIP_STATUS_COMPLETED]).
                '</span></a>',
                '<a href="'.route('trip.by_user_type',[$account->id, TRIP_CANCEL]).'" target="_blank" class="status_maroon status_txtsize">'.
                    $this->trip->totalTripByUserAndStatus($account->id, [TRIP_STATUS_CANCEL]).
                '</span></a>',
                '<a href="'.route('trip.by_user_type',[$account->id, TRIP_REJECT]).'" target="_blank" class="status_red status_txtsize">'.
                    $this->trip->totalTripByUserAndStatus($account->id, [TRIP_STATUS_REJECT]).
                '</a>',
                '<a href="'.route('trip.by_user_type',[$account->id, TRIP_SCHEDULE]).'" target="_blank" class="status_orange status_txtsize">'.
                    $this->trip->totalTripByUserAndStatus($account->id, [TRIP_STATUS_NEW]).
                '</a>',
                '<a href="'.route('trip.by_user_type',[$account->id, TRIP_ON_GOING]).'" target="_blank" class="status_blue status_txtsize">'.
                    $this->trip->totalTripByUserAndStatus($account->id, [TRIP_STATUS_ACCEPT, TRIP_STATUS_ARRIVED, TRIP_STATUS_JOURNEY_COMPLETED]).
                '</a>',
                "<div class='btn-group'>
                    <a href='".route('trip.by_user', [$account->id])."' target='_blank' class='btn  btn-success'>Views all Trip</a>
                </div>",
                '<div style="margin:5px;" class="btn-group">
                    <button style="width: 105px" data-toggle="dropdown" class="btn '.$status_class.' dropdown-toggle" aria-expanded="false">
                        '.$status_label.'<span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a onclick="changeStatus('.$account->id.','.$status_option_value.')">'.$status_option_label.'</a></li>
                        </ul>
                </div>',
                "<div class='btn-group'>
                    <button class='btn btn-danger btn-flat' data-toggle='modal' data-target='#modal-delete-confirmation' 
                    data-action-target='" . route('user.destroy', [$account->id]) . "'><i class='fa fa-trash'></i></button>
                </div>"
            ];
            $data[] = $tmp;
        }

        $total = $this->account->search($params, $order, $column, $offset, $limit, true);
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

    public function updateStatus(Request $request) {
        $uid    = intval($request->get('uid'));
        $status = intval($request->get('status'));

        if(!strlen($uid) || !strlen($status)) {
            return 0;
        }
        
        $account = $this->account->query()->where('id', $uid)->first();
        if(empty($account)) {
            return 0;
        }
        
        $account->status = $status;
        $is_save = $account->save();
        if($is_save)
            return 1;
        else
            return 0;        
    }
}