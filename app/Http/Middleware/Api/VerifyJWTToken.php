<?php

namespace App\Http\Middleware\Api;

use App\Http\Controllers\Api\ApiReponseFormatTrait;
use App\Models\Account;
use Closure;
use Illuminate\Http\Request;
use Auth;

class VerifyJWTToken
{
    use ApiReponseFormatTrait;

    /**
     * @var Account
     */
    private $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $auth = Auth::guard('api');
        $user = $auth->user();
        
        if (!$user) {
            return $this->errorUnauthorized();
        }        
        
        return $next($request);
    }
}
