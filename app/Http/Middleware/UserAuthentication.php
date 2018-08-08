<?php
/**
 * Created by PhpStorm.
 * User: tienvm
 * Date: 1/9/18
 * Time: 8:49 AM
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Config;
use \Firebase\JWT\JWT;

class UserAuthentication
{
    public function handle($request, Closure $next, $guard = null)
    {
        $user = $this->verifyToken(request());
        if(!$user){
            $permissionDenied = [
                'message' => 'Permission denied'
            ];
            return request()->json($permissionDenied, 401);
        }
        $request->attributes->add(['user' => $user]);
        return $next($request);
    }

    public function verifyToken(Request $request) {
        $token = $request->header('token');
        if(! $token) {
            $token = request()->get('token');
        }
        if(! $token){
            return false;
        }
        $decode_token = $this->deToken($token);
        $user = (array) $decode_token;
        return $user;
    }

    private function deToken($access_token) {
        $tokenArray = JWT::decode($access_token, Config::get('constants.key_token'), array('HS256'));
        return $tokenArray;
    }
}