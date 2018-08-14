<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;    
    
    public function getAvatar($filename) {
        $path = public_path('/upload/account/'.$filename);

        if(!File::exists($path)) abort(404);

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function stars($rate){
        $stars = '';
        for($i=1;$i<= 5;$i++){
            if($rate > 0) {
                $check = round(($i - $rate),1);
                if($check <= 0) {
                    $stars .= "<image src='".asset('assets/star.png')."'>&nbsp;</image>";
                } else {
                    if($check <= 1) {
                        if($check == 0.5)
                            $stars .= "<image src='".asset('assets/star-half-empty.png')."'>&nbsp;</image>";
                        else
                            $stars .= "<image src='".asset('assets/star-empty.png')."'>&nbsp;</image>";
                    } else {
                        $stars .= "<image src='".asset('assets/star-empty.png')."' >&nbsp;</image>";
                    }
                }
            } else {
                $stars .= "<image src='".asset('assets/star-empty.png')."' >&nbsp;</image>";
            }
        }
        return $stars;
    }
}
