<?php

namespace App\Models;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Mpociot\Firebase\SyncsWithFirebase;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class Account extends Authenticatable
{
    use Notifiable, SyncsWithFirebase;

    protected $table = 'account';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 'email', 'password', 'phone', 'status', 'created_at', 'updated_at',
        'token', 'type', 'remember_token', 'avatar', 'reset_token', 'latitude', 'longitude'
    ];
    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'token', 'reset_token'
    ];

    public function driverR() {
        return $this->hasOne(Driver::class, 'user_id');
    }

    public function tripR() {
        return $this->hasMany(Trip::class, 'user_id');
    }

    protected $appends = ['is_free', 'link_avatar'];
    public function getIsFreeAttribute()
    {
        return $this->checkDriverIsFree();
    }
    public function getLinkAvatarAttribute()
    {
        return $this->avatar ? asset('upload/driver/'.$this->avatar) : '';
    }

    public function checkDriverIsFree() {
        $query  = Trip::query()->where('driver_id', $this->id);
        $data = $query->get();
        if(!count($data))
            return 1;

        $is_free = 0;
        foreach($data as $item) {
            if($item->status == TRIP_STATUS_COMPLETED)
                $is_free = 1;
            else
                $is_free = 0;
        }
        return $is_free;
    }

    public static function checkTokenExist($token) {
        $query  = self::query();
        $query->where('token', $token)
              ->where('status', STATUS_ACTIVE);
        $data = $query->first();
        return $data ? $data : false;
    }  

    public static function checkEmailExist($email) {
        $query  = self::query()->where('email', $email);
        $data = $query->first();
        return $data ? $data : false;
    }

    public static function checkEmailAdminExist($email) {
        $query  = self::query()->where('email', $email)->where('type', TYPE_ADMIN);
        $data = $query->first();
        return $data ? $data : false;
    }

    public static function checkResetTokenExist($token) {
        $query  = self::query()->where('reset_token', $token)->where('type', TYPE_ADMIN);
        $data = $query->first();
        return $data ? $data : false;
    }

    public static function getAccountByEmailAndPassword($email, $password){
        $query  = self::query()->where('email', $email);
        $data = $query->first();
        $check = Hash::check($password, $data->password);
        return $check ? $data : false;
    }

    public static function insertData($data) {
        DB::transaction(function () use ($data) {
            //DB::table('account')->insert($data);
            //self::create($data);
            $account = new Account();
            foreach ($data as $key => $value) {
                $account->$key = $value;
            }
            $account->save();
        });
    }

    public static function updateData($condition, $data) {
        DB::transaction(function () use ($condition, $data) {
            DB::table('account')->where($condition)->update($data);
        });
    }

    public static function deleteAccount($condition) {
        DB::transaction(function () use ($condition) {
            DB::table('account')->where($condition)->delete();
        });
    }

    public static function checkPhoneExist($phone) {
        $query  = DB::table('account')->where('phone', $phone);
        $data = $query->first();
        return $data ? $data : false;
    }

    public static function getAll($pagination = 10) {
        $users = DB::table('account')->orderBy('id', 'desc')->paginate($pagination);
        return $users;
    }
    
    public static function getAccountByEmail($email) {
        $account = self::query()->where('email', $email)->first();
        return $account;
    }
    
    public function search($params, $order, $column, $offset, $limit, $count) {
        $query = self::query();
        $query->select('account.*');
        $query->where('type', TYPE_USER);

        $table = 'account';
        $query = $this->loadParams($query, $params, $table);

        if (isset($column)) {
            list($query, $column) = $this->getColumn($query, $column);
        }

        if ($order) {
            if(!$column)
                $query->orderBy('id', 'desc');
            else
                $query->orderBy($column, $order);
        }

        if ($count) {
            $user = intval($query->count());
        } else {
            if ($offset)
                $query->offset($offset);
            if ($limit)
                $query->limit($limit);

            $user = $query->get();
        }

        return $user;
    }

    public function loadParams($query, $params, $table) {
        if (isset($params['key'])) {
            $query->where(function ($query) use ($params, $table) {
                $query->where($table . '.full_name', 'like', '%' . $params['key'] . '%')
                    ->orWhere($table . '.phone', 'like', '%' . $params['key'] . '%');
            });
        }

        return $query;
    }

    public function getColumn($query, $column) {
        switch ($column) {
            case 0:
                $column = 'id';
                break;
        }

        return [$query, $column];
    }
    
    public static function getAllAccount() {
        return self::query()->where('type', TYPE_USER)->count();
    }
    
    public static function checkIsAdmin($email) {
        return self::query()->where([
            ['email', $email], ['type', TYPE_ADMIN]
        ])->first();
    }
}
