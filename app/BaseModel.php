<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    public function isNew(): bool {
        return Carbon::now()->diffInHours(new Carbon($this->updated_at)) < 24;
    }

	
}
