<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    public function actual()
    {
        $date = today()->subDays(7);
        return $this->where('created_at', '>=', $date)->latest()->get();
    }
    
}
