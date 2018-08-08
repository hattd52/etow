<?php
/**
 * Created by PhpStorm.
 * User: tienvm
 * Date: 12/21/17
 * Time: 10:36 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends ApiBaseController
{
    public function __construct(Request $request){
        parent::__construct($request);
    }
    
    public function create(Request $request)
    {
        $data = [];

        $comment = $request->get('comment');
        if(!$comment) {
            $this->message = 'Missing params.';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        $dataInsert = [
            'user_id' => $this->account->id,
            'comments' => $comment,
            'status' => STATUS_ACTIVE,
            'created_at' => date('Y-m-d H:i:s', time())
        ];
        Feedback::insertData($dataInsert);

        $this->message = 'send feedback successfully.';
        $this->status  = 'success';
        next:
        return $this->ResponseData($data);
    }
}