<?php
/**
 * Created by PhpStorm.
 * User: tienvm
 * Date: 12/21/17
 * Time: 10:36 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use Twilio\Rest\Client;

class TestController extends ApiBaseController
{
    public function test()
    {
        $this->message = 'ok';
        $this->status  = 'success';
        return $this->ResponseData();
    }

    public function testSMS() {
        $sid   = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $phone = env('MY_PHONE_NUMBER');
        $client = new Client($sid, $token);

        $client->messages->create(
            '+841679641067',
            [
                'from' => $phone,
                'body' => 'test SMS'
            ]
        );

        $this->status = STATUS_SUCCESS;
        $this->message = 'send OTP thanh cong';
        return $this->ResponseData();
    }
}