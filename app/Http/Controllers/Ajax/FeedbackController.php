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
use App\Models\Feedback;
use App\Models\Otp;
use App\Models\Price;
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

class FeedbackController extends Controller
{
    private $feedback;
    private $account;
    public function __construct(Feedback $feedback, Account $account){
        $this->feedback = $feedback;
        $this->account  = $account;
    }

    public function search(Request $request) {
        $key = $request->get('key');

        $orderName = $request->input('order');
        if ($orderName) {
            $order = $orderName[0]['dir'];
            $column = $orderName[0]['column'];
        } else {
            $order = null;
            $column = null;
        }

        $params = compact('key');
        $offset = $request->input('start');
        $limit  = $request->input('length');
        $data   = [];
        $total  = 0;

        $feedbacks = $this->feedback->search($params, $order, $column, $offset, $limit, false);
        if($feedbacks->isEmpty() == true){
            return $this->_getResponse($request, $total, $data);
        }

        $i = $offset;
        foreach ($feedbacks as $feedback){
            $i++;
            $tmp = [
                $i,
                $feedback->full_name,
                $feedback->phone,
                $feedback->comments,
                "<div class='btn-group'>
                    <button class='btn btn-danger btn-flat' data-toggle='modal' data-target='#modal-delete-confirmation' 
                    data-action-target='" . route('feedback.delete', [$feedback->id]) . "'><i class='fa fa-trash'></i></button>
                </div>"
            ];
            $data[] = $tmp;
        }

        $total = $this->feedback->search($params, $order, $column, $offset, $limit, true);
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

}