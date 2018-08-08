<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Http\Controllers\Api\ApiReponseFormatTrait;
class ApiBaseController extends Controller
{
    use ApiReponseFormatTrait;
    
    protected $user;

    public function __contruct(Request $request)
    {
        //$user = JWTAuth::user();
        //$user = JWTAuth::toUser($request->header('Authorization'));
        //$this->user = $user;
        //dd($this->user);
        $this->middleware('jwt.auth', ['except' => ['login']]);
        dd($user = JWTAuth::parseToken()->authenticate());
    }
}