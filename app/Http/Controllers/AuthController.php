<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Driver;
use App\Models\Trip;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    private $driver;
    private $account;
    private $trip;
    public function __construct(Account $account, Driver $driver, Trip $trip)
    {
        $this->account = $account;
        $this->driver = $driver;
        $this->trip = $trip;
        
        //$this->middleware('guest', ['except' => ['logout', 'getLogout']]);
    }

    use DispatchesJobs;
    
    public function dashBoard()
    {
        $total_users          = $this->account->getAllAccount();
        $total_drivers        = $this->driver->getTotalDriver();
        $total_trip_completed = $this->trip->totalTripByStatus([TRIP_STATUS_COMPLETED]);
        $total_trip_rejected  = $this->trip->totalTripByStatus([TRIP_STATUS_REJECT]);
        $total_trip_canceled  = $this->trip->totalTripByStatus([TRIP_STATUS_CANCEL]);
        $total_trip_ongoing   = $this->trip->totalTripByStatus([TRIP_STATUS_ACCEPT, TRIP_STATUS_ARRIVED, 
            TRIP_STATUS_JOURNEY_COMPLETED, TRIP_STATUS_ON_GOING]);
        $total_driver_free    = $this->driver->getTotalDriverFree();
        $total_driver_offline = $this->driver->getTotalDriverOffline();
        $data = [
            'total_users' => $total_users, 'total_drivers' => $total_drivers, 'total_trip_completed' => $total_trip_completed,
            'total_trip_rejected' => $total_trip_rejected, 'total_trip_canceled' => $total_trip_canceled,
            'total_trip_ongoing' => $total_trip_ongoing, 'total_driver_free' => $total_driver_free,
            'total_driver_offline' => $total_driver_offline
        ];
        return view('dashboard', compact('data'));
    }

    public function index()
    {
        return view('dashboard');
    }
    
    public function getLogin()
    {
        return view('auth.login');
    }

    public function postLogin(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        
        // check is admin
        $account = Account::checkIsAdmin($request->email);
        if(empty($account)) {
            $msg = trans('auth.messages.not permission');
            return back()->with('error', $msg);
        }

        $remember = (bool) $request->get('remember_me', false);

        $success = Auth::attempt($credentials, $remember);

        if (!$success) {
            //dd(123);
            $msg = trans('auth.messages.failed logged in');
            return back()->with('error', $msg);
        }
        //dd(456);
        return redirect()->route('dashboard')
            ->withSuccess(trans('auth.messages.successfully logged in'));
    }

    public function getRegister()
    {
        return view('user::public.register');
    }

    public function postRegister(RegisterRequest $request)
    {
        app(UserRegistration::class)->register($request->all());

        return redirect()->route('register')
            ->withSuccess(trans('user::messages.account created check email for activation'));
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function getActivate($userId, $code)
    {
        if ($this->auth->activate($userId, $code)) {
            return redirect()->route('login')
                ->withSuccess(trans('user::messages.account activated you can now login'));
        }

        return redirect()->route('register')
            ->withError(trans('user::messages.there was an error with the activation'));
    }
    
    public function forgotPassword(Request $request) {        
        return view('auth.forgot');
    }

    public function postForgotPassword(Request $request) {
        $email = $request->get('email');
        $check = Account::checkEmailAdminExist($email);
        if(empty($check)) {
            $msg = trans('auth.messages.email incorrect');
            return back()->with('error', $msg);
        }

        $token = md5($email.rand(0,1000).time());
        //Account::updateData(['email', $email],['reset_token' => $token]);
        $check->reset_token = $token;
        if($check->save()) {
            $dataSend = [
                'from'         => env('MAIL_FROM_ADDRESS', 'fruity.tester@gmail.com'),
                'sendName'     => env('MAIL_FROM_NAME', 'eTow system'),
                'subject'      => 'Forgot password.',
                'email'        => $email,
                'name'         => $check->full_name,
                'reset_token'  => $check->reset_token,
                'url_reset'    => route('reset-password'),
            ];
            $template = 'forgot_password';
            $this->sendMail($dataSend, $template);
        }        
        
        return redirect()->route('forgot-password')
            ->withSuccess(trans('auth.messages.forgot successfully'));
    }

    public function getResetPassword()
    {
        return view('auth.reset');
    }

    public function postResetPassword(Request $request)
    {
        $token            = trim($request->get('token'));
        $new_password     = trim($request->get('new_password'));
        $confirm_password = trim($request->get('confirm_password'));

        $check = Account::checkResetTokenExist($token);
        if(empty($check)) {
            $msg = 'Token mismatch.';
            return back()->with('error', $msg);
        }

        if($new_password !== $confirm_password) {
            $msg = 'New password and confirm password must be the same.';
            return back()->with('error', $msg);
        }

        $check->password = Hash::make($new_password);
        if($check->save()) {
            $check->reset_token = '';
            $check->save();
        }

        return redirect()->route('login')
            ->withSuccess(trans('auth.messages.reset successfully'));
    }

    public function getResetComplete()
    {
        return view('user::public.reset.complete');
    }

    public function postResetComplete($userId, $code, ResetCompleteRequest $request)
    {
        try {
            app(UserResetter::class)->finishReset(
                array_merge($request->all(), ['userId' => $userId, 'code' => $code])
            );
        } catch (UserNotFoundException $e) {
            return redirect()->back()->withInput()
                ->withError(trans('user::messages.user no longer exists'));
        } catch (InvalidOrExpiredResetCode $e) {
            return redirect()->back()->withInput()
                ->withError(trans('user::messages.invalid reset code'));
        }

        $user = User::find($userId);

        if ($user && ($user->hasRoleSlug('employee') || $user->hasRoleSlug('manager'))) {
            return redirect('/forgot-password-complete');
        }

        return redirect()->route('login')
            ->withSuccess(trans('user::messages.password reset'));
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
}
