<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Price extends Model
{
    const 
        STATUS_ACTIVE = 1,
        STATUS_INACTIVE = 0;
    
    protected $table = 'price';
    //protected $connection = 'db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'km', 'price', 'unit', 'status', 'created_at', 'updated_at', 'min', 'max'
    ];
    public $timestamps = false;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'avatar'
    ];

    public static function getList() {
        return $users = DB::table('price')->where('status', self::STATUS_ACTIVE)->get();
    }
    
    public static function getPriceByDistance($distance) {
        return $price = DB::table('price')
        ->select('price')
        ->where('status', self::STATUS_ACTIVE)
        ->where('min', '<=', $distance)
        ->where('max', '>=', $distance)
        ->first();
    }
    
    public function search($params, $order, $column, $offset, $limit, $count) {
        $query = self::query();
        $query->select('price.*');
        $query->where('status', STATUS_ACTIVE);
        $table = 'price';
        $query = $this->loadParams($query, $params, $table);

        if (isset($column)) {
            list($query, $column) = $this->getColumn($query, $column);
        }

        if ($order) {
            if(!$column)
                $query->orderBy('id', 'asc');
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
        return $query;
    }

    public function getColumn($query, $column) {
        switch ($column) {
            case 0:
                $column = 'min';
                break;            
        }

        return [$query, $column];
    }
}
