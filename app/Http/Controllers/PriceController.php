<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    private $driver;
    private $account;
    private $trip;
    public function __construct(Driver $driver, Account $account, Trip $trip) {
        $this->driver  = $driver;
        $this->account = $account;
        $this->trip    = $trip;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('price.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if($request->hasFile('filePrice')){
            $path = $request->file('filePrice')->getRealPath();
            $data = \Excel::load($path)->get();
            if($data->count()){
                foreach ($data as $key => $value) {
                    $item = $value->toArray();
                    $km = $item['km'];
                    if(substr_count($km, '-')) {
                        $minMax = explode('-', $km);
                        $min    = trim($minMax[0]);
                        $max    = trim($minMax[1]);
                    } elseif (substr_count($km, '+')) {
                        $minMax = explode('+', $km);
                        $min    = trim($minMax[0]);
                        $max    = time();
                    } else {
                        $min = $max = $km;
                    }
                    $arr[] = ['km' => $km, 'price' => $item['price'], 'unit' => $item['unit'], 'min' => $min, 'max' => $max];
                }

                if(!empty($arr)){
                    DB::table('price')->truncate();
                    DB::table('price')->insert($arr);
                }
            }
        }

        return redirect()->route('setting.index')->withSuccess(trans('setting.message.update success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updatePrice() {        
        return view('price.update');
    }
}
