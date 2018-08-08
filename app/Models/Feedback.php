<?php

namespace App\Models;

use App\Models\Account;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mpociot\Firebase\SyncsWithFirebase;

class Feedback extends Model
{
    use SyncsWithFirebase;
    
    const 
        STATUS_ACTIVE = 1,
        STATUS_INACTIVE = 0;
    
    protected $table = 'feedback';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'comments', 'status'
    ];
    //public $timestamps = false;

    public function userR()
    {
        return $this->belongsTo(Account::class, 'user_id');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'avatar'
    ];

    public static function insertData($data) {
        DB::transaction(function () use ($data) {
            $feedback = new Feedback();
            foreach ($data as $key => $value) {
                $feedback->$key = $value;
            }
            $feedback->save();
        });
    }

    public static function updateData($condition, $data) {
        DB::transaction(function () use ($condition, $data) {
            DB::table('feedback')->where($condition)->update($data);
        });
    }

    public static function deleteAccount($condition) {
        DB::transaction(function () use ($condition) {
            DB::table('feedback')->where($condition)->delete();
        });
    }

    public static function getList() {
        return self::query()->where('status', self::STATUS_ACTIVE)->get();
    }
    
    public function search($params, $order, $column, $offset, $limit, $count) {
        $query = self::query();
        $query->join('account', 'account.id', '=', 'feedback.user_id');
        $query->select('feedback.*', 'account.full_name', 'account.phone');
        $query->where('feedback.status', STATUS_ACTIVE);
        $table = 'feedback';
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
        if(isset($params['key']) && $params['key']) {
            $query->where(function ($query) use ($params, $table) {
                $table_join  = 'account';
                $query->where($table_join . '.phone', 'like', '%' . $params['key'] . '%')
                    ->orWhere($table_join . '.full_name', 'like', '%' . $params['key'] . '%');
            });
        }
        return $query;
    }

    public function getColumn($query, $column) {
        switch ($column) {
            case 0:
                $column = 'feedback.id';
                break;
            case 1:
                $column = 'account.full_name';
                break;
            case 2:
                $column = 'account.phone';
                break;
        }

        return [$query, $column];
    }
}
