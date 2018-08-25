<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\UserToken;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;

class ApiBaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $status  = STATUS_ERROR;

    protected $message = '';

    protected $account;
    protected $authorization = false;
    protected $http_code = 200;
    public function __construct(Request $request) {
        $this->getUserLogin($request);
    }

    protected function ResponseData($data = [], $more = '') {
        $res = [
            'status'  => $this->status,
            'message' => $this->message,
            'data'    => $data ? $data : (object)$data
        ];
        if($more)
            $res = array_merge($res, $more);
        return response()->json($res, $this->http_code);
    }

    public function setRedis($key, $data)
    {
        return \Redis::set($key, json_encode($data, JSON_FORCE_OBJECT));
    }
    public function expireTime($key, $time)
    {
        return \Redis::expire($key, $time);
    }

    public function getRedis($key)
    {
        $result = \Redis::get($key);
        return (array)json_decode($result, true);
    }

    public function deleteRedis($key)
    {
        $result = \Redis::del($key);
        return true;
    }
    private function _language () {
        if(!isset($_REQUEST['lang']))
            return false;
        $lang = !$_REQUEST['lang'] ? 'en' : $this->listLang()[$_REQUEST['lang']];
        return App::setLocale($lang);
    }

    private function listLang() {
        return $arr =
            [
                'vi-VN' => 'vi',
                'en-US' => 'en'
            ];
    }

    public function downloadExport($filename) {
        $path = storage_path('exports') . '/' . $filename;
        if(!File::exists($path)) {
            $path_public = public_path().'/uploads/'.$filename;
            if(!File::exists($path_public)) {
                //abort(404);
                $this->message = 'Không tìm thấy file.';
                return $this->ResponseData();
            }  else
                $path = $path_public;
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }    

    public function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }   

    public static function sendMail($data, $template)
    {
        $send = \Mail::send('emails.'.$template, ['data' => $data], function ($message) use ($data) {
            $message->from($data['from'], $data['sendName'])
                ->subject($data['subject'])
                ->to($data['email']);
                if(!empty($data['attach'])) {
                    $message->attach($data['attach']);
                }
                if(!empty($data['bcc'])) {
                    $message->bcc($data['bcc']);
                }                
        });

        return $send;
    }
    
    public static function _getOTP() {
        $alphabet = "0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
    
    public static function sendOTP($phone, $otp) {
        $sid   = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from  = env('MY_PHONE_NUMBER');
        $client = new Client($sid, $token);        
        
        try {
            $message = $client->messages->create(
                $phone,
                [
                    'from' => $from,
                    'body' => 'Your OTP verification code is: '.$otp,
                    'statusCallback' => 'http://suusoft.com/eTow/public/api/v1/user/get-otp?phone=+84972389223',
                ]
            );
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $res     = ['status' => STATUS_ERROR, 'message' => $message, 'data' => ""];
            //dd($message);
            //$this->message = $message;
            //echo $this->ResponseData(); die;
            //http_response_code(SEND_OTP_ERROR);
            header('Content-Type: application/json');
            http_response_code(SEND_OTP_ERROR);
            echo json_encode($res); exit;
        }
    }
    
    public static function convertValueObject($object) {
        $array = $object->toArray();
        foreach ($array as $key => $value) {
            $array[$key] = $value ? $value : '';
        }
        return $array;
    }

    public function getUserLogin(Request $request) {
        $token   = $request->header('Authorization');
        if($token) {
            $account = Account::checkTokenExist($token);
            $this->account = $account;
        }

        return true;
    }
}
