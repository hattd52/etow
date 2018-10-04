<?php
/**
 * Created by PhpStorm.
 * User: tienvm
 * Date: 12/21/17
 * Time: 10:36 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\Account;
use App\Models\Device;
use App\Models\Driver;
use App\Models\Otp;
use App\Models\UserToken;
use App\Transformers\Api\AccountTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Exceptions\JWTException;
use \Illuminate\Support\Facades\Auth;
use App\Models\Trip;


class AccountController extends ApiBaseController
{
    public function __construct(Request $request){
        parent::__construct($request);
    }

    public function register(Request $request)
    {
        list($email, $password, $phone, $full_name, $avatar) = $this->_getParams($request);

        $data = [];
        if(!$email || !$password || !$phone) {
            $this->message = 'Missing params. Please try again.';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        $checkEmailExist = Account::checkEmailExist($email);
        if(!empty($checkEmailExist)) {
            $this->message = 'Email existed, please try again.';
            $this->http_code = EMAIL_EXIST;
            goto next;
        }

        $imageName = '';
        if($avatar) {
            //$image_ext = $avatar->getClientOriginalExtension();
            //$imageName = time()  . 'avatar.' . $image_ext;

            $imgdata   = base64_decode($avatar);
            $f         = finfo_open();
            $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
            $type_file = explode('/',$mime_type);
            $imageName = time().'avatar.'.$type_file[1];
        }

        $token = Hash::make(md5($email));
        $dataInsert = [
            'email'      => $email,
            'password'   => Hash::make($password),
            'phone'      => $phone,
            'full_name'  => $full_name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'token'      => $token,
            'avatar'     => $imageName
            //'avatar'     => URL::to('/get-avatar/'.$imageName)
        ];
        Account::insertData($dataInsert);

        if($avatar) {
            $file_path = public_path('upload/account');
            //$avatar->move($file_path, $imageName);
            file_put_contents($file_path.DIRECTORY_SEPARATOR.$imageName, base64_decode($avatar));
        }

        $account = Account::getAccountByEmail($email);
        $data = new AccountTransformer($account);
        Auth::login($account, true);

        $this->message = 'Register successfully.';
        $this->status  = 'success';

        next:
        return $this->ResponseData($data);
    }

    public function _getParams(Request $request) {
        $email     = $request->get('email');
        $password  = $request->get('password');
        $phone     = $request->get('phone');
        $full_name = $request->get('full_name');
        $avatar    = $request->get('avatar');
        return [$email, $password, $phone, $full_name, $avatar];
    }

    public function login(Request $request) {
        list($email, $password, $phone, $full_name) = $this->_getParams($request);
        $data = [];
        if(!$email || !$password) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        $credentials = $request->only('email', 'password');
        $token = null;
        try {
            if (!$check = Auth::attempt($credentials)) {
                $this->message = 'Email or password incorrect.';
                $this->http_code = EMAIL_OR_PASSWORD_INCORRECT;
                goto next;
            }
        } catch (\Exception $e) {
            $this->message = 'failed_to_create_token.';
            goto next;
        }

        /** @var Account $account */
        $account        = Auth::user();
        if($account->status == STATUS_INACTIVE) {
            Auth::logout();
            $this->message = 'Account is inactive.';
            $this->http_code = ACCOUNT_INACTIVE;
            goto next;
        }

        $token = Hash::make(md5($email));
        $account->token = $token;
        $account->save();

        Auth::login($account, true);
        $account = Account::query()->where('email', $email)->first();
        //$data = AccountTransformer::collection($account);
        $data = new AccountTransformer($account);

        // update device
        $this->updateDevice($account->id, $request);

        $this->status  = 'success';
        $this->message = 'Login successfully';

        next:
        return $this->ResponseData($data);
    }

    public function updateDevice($user_id, Request $request) {
        $ime    = $request->get('ime');
        $token  = $request->get('token');

        $checkDeviceUser = Device::checkUserExist($user_id);
        if(!empty($checkDeviceUser)) {
            $dataUpdate = [
                'ime'   => $ime,
                'token' => $token
            ];
            Device::updateData(['user_id' => $user_id], $dataUpdate);
        } else {
            $dataInsert = [
                'user_id' => $user_id,
                'ime'     => $ime,
                'token'   => $token,
                'status'  => STATUS_ACTIVE,
                'created_at' => date('Y-m-d H:i:s', time())
            ];
            Device::insertData($dataInsert);
        }
    }

    public function logout(Request $request)
    {
        /** @var Account $account */
        //$account = Auth::user();
        $account = $this->account;
        $account->token = '';
        $account->save();

        try {
            Auth::logout();
            $this->message = 'User successfully logged out.';
            $this->status  = STATUS_SUCCESS;
            goto next;
        } catch (\Exception $e) {
            $this->message = 'Failed to logout, please try again.';
            $this->http_code = LOGOUT_FAILED;
            goto next;
        }

        next:
        return $this->ResponseData();
    }

    public function updateProfile(Request $request) {
        $data = [];
        list($email, $password, $phone, $full_name, $avatar) = $this->_getParams($request);
        $is_online = $request->get('is_online');
        $token     = $request->header('Authorization');
        if(!strlen($is_online)) {
            if(!$password) {
                $this->message = 'Password missing.';
                $this->http_code = PASSWORD_MISSING;
                return $this->ResponseData($data);
            }

            $account   = Account::checkTokenExist($token);
            $oldAvatar = $account->avatar;
            if(!strlen($is_online)) {
                $check     = Hash::check($password, $account->password);
                if(!$check) {
                    $this->message = 'Password incorrect.';
                    $this->http_code = PASSWORD_INCORRECT;
                    return $this->ResponseData($data);
                }
            }

            $imageName = '';
            if($avatar) {
                //$image_ext = $avatar->getClientOriginalExtension();
                //$imageName = time()  . 'avatar.' . $image_ext;

                $imgdata   = base64_decode($avatar);
                $f         = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                $type_file = explode('/',$mime_type);
                $imageName = time().'avatar.'.$type_file[1];
            }

            $dataUpdate = [];
            if($phone)
                $dataUpdate['phone'] = $phone;
            if($full_name)
                $dataUpdate['full_name'] = $full_name;
            if($avatar)
                $dataUpdate['avatar'] = $imageName;
            Account::updateData(['token' => $token], $dataUpdate);

            if($avatar) {
                $file_path = public_path('upload/account');
                //$avatar->move($file_path, $imageName);
                file_put_contents($file_path.DIRECTORY_SEPARATOR.$imageName, base64_decode($avatar));

                if($oldAvatar && file_exists($file_path.DIRECTORY_SEPARATOR.$oldAvatar)) {
                    //$oldAvatar = substr($oldAvatar, strrpos($oldAvatar,'/') + 1);
                    unlink($file_path.DIRECTORY_SEPARATOR.$oldAvatar);
                }
            }
        }

        $account = Account::checkTokenExist($token);
        $data    = new AccountTransformer($account);

        if(strlen($is_online)) {
            $driver = Driver::query()->where('user_id', $account->id)->first();
            if($driver){
                $driver->is_online = $is_online;
                $driver->save();
            }
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'update successfully';
        return $this->ResponseData($data);
    }

    public function forgotPassword(Request $request) {
        list($email, $password, $phone, $full_name) = $this->_getParams($request);

        $checkEmailExist = Account::checkEmailUserExist($email);
        if(empty($checkEmailExist)) {
            $this->message = 'Email does not exist.';
            $this->http_code = EMAIL_DOES_NOT_EXIST;
            goto next;
        }

        $new_password = $this->randomPassword();
//        $checkEmailExist->password = Hash::make($new_password);
//        $checkEmailExist->save();
        Account::updateData(['email' => $email], ['password' => Hash::make($new_password)]);

        $dataSend = [
            'from'         => env('MAIL_FROM_ADDRESS', 'fruity.tester@gmail.com'),
            'sendName'     => env('MAIL_FROM_NAME', 'eTow system'),
            'subject'      => 'Reset password.',
            'email'        => $email,
            'name'         => $checkEmailExist->full_name,
            'new_password' => $new_password,
        ];
        $template = 'reset_password';
        $this->sendMail($dataSend, $template);

        $this->status  = STATUS_SUCCESS;
        $this->message = 'Reset password successfully, Please check your email.';

        next:
        return $this->ResponseData();
    }

    public function getOTP(Request $request) {
        $phone = $request->get('phone');

        if(!$phone) {
            $this->message = 'Missing params.';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        $otp = $this->_getOTP();
        $this->sendOTP($phone, $otp);

        $check = Otp::checkPhoneExist($phone);
        if(!empty($check)) {
            $dataUpdate = [
                'otp' => $otp,
                'updated_at' => date("Y-m-d H:i:s",time())
            ];
            Otp::updateData(['phone' => $phone], $dataUpdate);
        } else {
            $new_otp             = new Otp();
            $new_otp->phone      = $phone;
            $new_otp->otp        = $otp;
            $new_otp->created_at = date("Y-m-d H:i:s",time());
            $new_otp->save();
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'get OTP successful.';

        next:
        return $this->ResponseData();
    }

    public function verifyOtp(Request $request) {
        $otp = $request->get('otp');

        if(!$otp) {
            $this->message = 'Missing params.';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        $otp = Otp::checkOtp($otp);
        if(empty($otp)) {
            $this->message = 'Otp incorrect.';
            $this->http_code = OTP_INCORRECT;
            goto next;
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'verify OTP successful.';

        next:
        return $this->ResponseData();
    }

    public function rateTrip(Request $request) {
        $data = [];
        $trip_id = $request->get('trip_id');
        $rate = $request->get('rate');
        if(!$trip_id || !strlen($rate)) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        /** @var Trip $trip */
        $trip      = Trip::find($trip_id);
        if(!empty($trip)) {
            if($this->account->id != $trip->user_id) {
                $this->message = 'You are not permission with this trip';
                $this->http_code = TRIP_NOT_PERMISSION;
                goto next;
            }

            $trip->rate  = $rate;
            $trip->save();
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'rate trip successfully';

        next:
        return $this->ResponseData($data);
    }

    public function updateLocation(Request $request) {
        $data = [];
        $lat  = $request->get('latitude');
        $long = $request->get('longitude');
        if(!$lat || !$long) {
            $this->message = 'Missing params';
            $this->http_code = MISSING_PARAMS;
            goto next;
        }

        /** @var Account $account */
        $account = Account::find($this->account->id);
        if(!empty($account)) {
            $account->latitude  = $lat;
            $account->longitude = $long;
            $account->save();
        }

        $this->status  = STATUS_SUCCESS;
        $this->message = 'update location successfully';

        next:
        return $this->ResponseData($data);
    }
}