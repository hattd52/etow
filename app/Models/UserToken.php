<?php

namespace App\Models;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserToken extends Model
{
    protected $table = 'user_tokens';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'access_token', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    public function userR() {
        return $this->belongsTo(Account::class, 'user_id');
    }
    
    public static function checkTokenExist($token) {
        $query  = DB::table('user_tokens')->where('token', $token);
        $data = $query->first();
        return $data ? $data : false;
    }

    public static function checkUserExist($uid) {
        $query  = DB::table('user_tokens')->where('user_id', $uid);
        $data = $query->first();
        return $data ? $data : false;
    }

    public static function getAccountByEmailAndPassword($email, $password){
        $query  = DB::table('account')
            ->where('email', $email);
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
}
