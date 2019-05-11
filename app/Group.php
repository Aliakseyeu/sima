<?php

namespace App;


class Group extends BaseModel
{
    
    protected $fillable = ['user_id', 'status_id', 'processed_at'];

    public function status(){
        return $this->belongsTo('App\Status');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function orders(){
        return $this->hasMany('App\Order');
    }

    public function getOrderBySid(int $sid): Order {
        return $this->orders()->whereHas('item', function($q) use ($sid){
            return ($q->where('sid', $sid));
        })->get()->first();
    }

}
