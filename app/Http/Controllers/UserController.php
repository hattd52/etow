<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Driver;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $driver;
    private $account;
    public function __construct(Driver $driver, Account $account)
    {
        $this->driver  = $driver;
        $this->account = $account;
    }
    
    public function index()
    {
        return view('user.index');
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
        $account = Account::find($id);
        if(!empty($account)) {
            $oldAvatar = $account->avatar;
            $path = public_path('upload/account');
            if($oldAvatar && file_exists($path.DIRECTORY_SEPARATOR.$oldAvatar)) {
                unlink($path.DIRECTORY_SEPARATOR.$oldAvatar);
            }
            
            Account::destroy($id);
        }
        
        return redirect()->route('user.index')
            ->withSuccess(trans('user.message.destroy success'));
    }
}
