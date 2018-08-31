<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Driver\CreateDriverRequest;
use App\Http\Requests\Admin\Driver\UpdateDriverRequest;
use App\Http\Requests\Admin\DriverRequest;
use App\Models\Account;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class DriverController extends Controller
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
        $total =  $this->driver->getTotalDriver();
        return view('driver.index', compact('total'));
    }

    public function indexByType($type)
    {
        $total =  $this->driver->getTotalDriver();
        return view('driver.index', compact('total', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Driver $driver)
    {
        $driver_code = $this->driver->getNextDriverCode();
        return view('driver.create', compact('driver', 'driver_code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDriverRequest $request)
    {
        DB::beginTransaction();
        list($avatar, $avatarName, $license, $licenseName, $emirate, $emirateName, $mulkiya, $mulkiyaName) =
            $this->_getFileUpload($request);

            $account = $this->_createAccount($request, $avatarName);
            if(!empty($account)) {
                if ($avatar) {
                    $path = public_path('upload/account');
                    $avatar->move($path, $account->avatar);
                }
                list($is_save, $driver) = $this->_createDriver($account, $request, $licenseName, $emirateName, $mulkiyaName);
                if (!empty($is_save)) {
                    $this->_uploadFileDriver($license, $emirate, $mulkiya, $driver);
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            } else {
                DB::rollBack();
            }

        return redirect()->route('driver.index')->withSuccess(trans('driver.message.create success'));
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
    public function edit(Driver $driver)
    {
        return view('driver.edit', compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function _createAccount($request, $avatarName) {
        $dataUserInsert = [
            'full_name' => $request->get('full_name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'phone'    => $request->get('phone'),
            'status'   => STATUS_ACTIVE,
            'type'     => TYPE_DRIVER,
            'avatar'   => $avatarName,
            'created_at' => date('Y-m-d H:i:s', time()),
        ];
        $account = $this->account->create($dataUserInsert);
        return $account;
    }

    public function _createDriver($account, $request, $licenseName, $emirateName, $mulkiyaName) {
        $driver = new Driver();
        $driver->user_id = $account->id;
        $driver->driver_code = $request->get('driver_code');
        $driver->vehicle_type = $request->get('vehicle_type');
        $driver->vehicle_number = $request->get('vehicle_number');
        $driver->company_name = $request->get('company_name');
        $driver->is_online = 1;
        $driver->driver_license = $licenseName;
        $driver->emirate_id = $emirateName;
        $driver->mulkiya = $mulkiyaName;
        $driver->created_at = date('Y-m-d H:i:s', time());
        $is_save = $driver->save();
        return [$is_save, $driver];
    }

    public function _uploadFileDriver($license, $emirate, $mulkiya, $driver, $oldLicense = false,
        $oldEmirate = false, $oldMulkiya = false) {
        $file_path = public_path('upload/driver');
        if ($license) {
            $license->move($file_path, $driver->driver_license);
            if($oldLicense) {
                unlink($file_path.DIRECTORY_SEPARATOR.$oldLicense);
            }
        }
        if ($emirate) {
            $emirate->move($file_path, $driver->emirate_id);
            if($oldEmirate) {
                unlink($file_path.DIRECTORY_SEPARATOR.$oldEmirate);
            }
        }
        if ($mulkiya) {
            $mulkiya->move($file_path, $driver->mulkiya);
            if($oldMulkiya) {
                unlink($file_path.DIRECTORY_SEPARATOR.$oldMulkiya);
            }
        }
    }

    public function update(Driver $driver, UpdateDriverRequest $request)
    {
        DB::transaction(function () use ($request, $driver) {
            list($avatar, $avatarName, $license, $licenseName, $emirate, $emirateName, $mulkiya, $mulkiyaName) =
                $this->_getFileUpload($request);

            $this->_updateAccount($request, $driver, $avatar, $avatarName);
            $this->_updateDriver($request, $driver, $license, $licenseName, $emirate, $emirateName,
                $mulkiya, $mulkiyaName);
        });

        return redirect()->route('driver.index')->withSuccess(trans('driver.message.update success'));
    }

    public function _getFileUpload($request) {
        $avatar  = $request->file('avatar');
        $license = $request->file('driver_license');
        $emirate = $request->file('emirate_id');
        $mulkiya = $request->file('mulkiya');

        $avatarName = '';
        if($avatar) {
            $image_ext  = $avatar->getClientOriginalExtension();
            $avatarName = time() .rand(0,1000). 'avatar.' . $image_ext;
        }

        $licenseName = '';
        if ($license) {
            $image_ext   = $license->getClientOriginalExtension();
            $licenseName = time() . rand(0, 1000) . 'license.' . $image_ext;
        }

        $emirateName = '';
        if ($emirate) {
            $image_ext   = $emirate->getClientOriginalExtension();
            $emirateName = time() . rand(0, 1000) . 'emirate.' . $image_ext;
        }

        $mulkiyaName = '';
        if ($mulkiya) {
            $image_ext   = $mulkiya->getClientOriginalExtension();
            $mulkiyaName = time() . rand(0, 1000) . 'mulkiya.' . $image_ext;
        }

        return [$avatar, $avatarName, $license, $licenseName, $emirate, $emirateName, $mulkiya, $mulkiyaName];
    }

    public function _updateAccount($request, $driver, $avatar, $avatarName) {
        $account = Account::find($driver->user_id);
        $oldAvatar = $account->avatar;
        $account->full_name = $request->get('full_name');
        $account->phone     = $request->get('phone');
        if($avatarName) {
            $account->avatar = $avatarName;
        }
        $is_save = $account->save();
        if($is_save && $avatarName) {
            $path = public_path('upload/account');
            $avatar->move($path, $account->avatar);

            if($oldAvatar) {
                unlink($path.DIRECTORY_SEPARATOR.$oldAvatar);
            }
        }
    }

    public function _updateDriver($request, $driver, $license, $licenseName, $emirate, $emirateName,
        $mulkiya, $mulkiyaName) {
        $oldLicense = $driver->driver_license;
        $oldEmirate = $driver->emirate_id;
        $oldMulkiya = $driver->mulkiya;

        $driver->company_name = $request->get('company_name');
        $driver->vehicle_type = $request->get('vehicle_type');
        $driver->vehicle_number = $request->get('vehicle_number');
        if($licenseName) {
            $driver->driver_license = $licenseName;
        }
        if($emirateName) {
            $driver->emirate_id = $emirateName;
        }
        if($mulkiyaName) {
            $driver->mulyika = $mulkiyaName;
        }
        $is_save = $driver->save();
        if($is_save) {
            $this->_uploadFileDriver($license, $emirate, $mulkiya, $driver, $oldLicense, $oldEmirate, $oldMulkiya);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $driver = Driver::find($id);
        if(!empty($driver)) {
            $oldLicense = $driver->driver_license;
            $oldEmirate = $driver->emirate_id;
            $oldMulkiya = $driver->mulkiya;            
            $path = public_path('upload/driver');
            if($oldLicense && file_exists($path.DIRECTORY_SEPARATOR.$oldLicense)) {
                unlink($path.DIRECTORY_SEPARATOR.$oldLicense);
            }
            if($oldEmirate && file_exists($path.DIRECTORY_SEPARATOR.$oldEmirate)) {
                unlink($path.DIRECTORY_SEPARATOR.$oldEmirate);
            }
            if($oldMulkiya && file_exists($path.DIRECTORY_SEPARATOR.$oldMulkiya)) {
                unlink($path.DIRECTORY_SEPARATOR.$oldMulkiya);
            }
            Driver::destroy($id);

            $account = Account::find($driver->user_id);
            if(!empty($account)) {
                $oldAvatar = $account->avatar;
                $path = public_path('upload/account');
                if($oldAvatar && file_exists($path.DIRECTORY_SEPARATOR.$oldAvatar)) {
                    unlink($path.DIRECTORY_SEPARATOR.$oldAvatar);
                }
                Account::destroy($driver->user_id);
            }
        }

        return redirect()->route('driver.index')
            ->withSuccess(trans('driver.message.destroy success'));
    }
}
