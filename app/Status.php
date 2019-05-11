<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    
    public function groups(){
        return $this->hasMany('App\Group');
    }

    public function archived(){
        return $this->where('slug', 'archived')->first();
    }

    public function isArchived(): bool
    {
        return $this->slug == 'archived';
    }

    public function new(){
        return $this->where('slug', 'new')->first();
    }

}
