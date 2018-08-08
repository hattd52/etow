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
use JWTAuth;
use Exception;

class AuthJWT
{
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
        try {
            $user = JWTAuth::toUser($request->header('Authorization'));
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                $message = 'Token is Invalid.';
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                $message = 'Token is Expired.';
            }else{
                $message = 'Token is missing.';
            }

            return $this->_getErrorResponse($message);
        }
        return $next($request);
    }

    public static function _getErrorResponse($message) {
        return response()->json([
            'status'  => STATUS_ERROR,
            'message' => $message,
            'data'    => ''
        ]);
    }
}