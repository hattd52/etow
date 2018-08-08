<?php
/**
 * Created by PhpStorm.
 * User: tienvm
 * Date: 12/21/17
 * Time: 10:36 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends ApiBaseController
{
    public function __construct(Request $request){
        parent::__construct($request);
    }
    
    public function index(Request $request)
    {
        $data = [];
        
        $prices = Price::getList();
        if(!empty($prices)) {
            foreach ($prices as $price) {
                $data[] = $price;
            }
        }

        $this->message = 'get list price successfully.';
        $this->status  = 'success';
        return $this->ResponseData($data);
    }
}