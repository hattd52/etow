<?php
/**
 * Created by PhpStorm.
 * User: Yoona
 * Date: 14/07/2018
 * Time: 10:16 CH
 */

namespace App\Http\Middleware;

use App\Models\Account;
use Closure;
use Illuminate\Support\Facades\Auth;

class AuthApi
{
    protected $account;
    public function __construct()
    {
        //
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {                
        $token = $request->header('Authorization');
        if(!$token) {
            $message = 'Token missing';
            return $this->_getErrorResponse($message);
        }

        $check = Account::checkTokenExist($token);
        if(empty($check)) {
            $message = 'Token miss match';
            return $this->_getErrorResponse($message, 510);
        }

        $this->account = Auth::user();
        
        return $next($request);
    }

    public static function _getErrorResponse($message, $status = 200) {
        return response()->json([
            'status'  => STATUS_ERROR,
            'message' => $message,
            'data'    => ''
        ], $status);
    }
}