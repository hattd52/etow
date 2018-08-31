<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
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
        $total = $this->trip->getTotalTrip();
        return view('trip.index', compact('total'));
    }

    public function indexByType($type)
    {
        $total = $this->trip->getTotalTrip();
        return view('trip.index', compact('total', 'type'));
    }

    public function byUser($user_id)
    {
        $total = $this->trip->getTotalTrip();        
        return view('trip.index', compact('total', 'user_id'));
    }
    
    public function typeByUser($user_id, $type)
    {
        $total = $this->trip->getTotalTrip();
        $by    = TRIP_BY_USER;
        return view('trip.index', compact('total', 'user_id', 'type', 'by'));
    }

    public function typeByDriver($driver_id, $type)
    {
        $total = $this->trip->getTotalTrip();
        $by    = TRIP_BY_DRIVER;
        return view('trip.index', compact('total', 'driver_id', 'type', 'by'));
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
    public function update(Request $request, $id)
    {
        //
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
}
