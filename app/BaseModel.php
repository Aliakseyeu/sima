<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    public function isNew(): bool {
        return Carbon::now()->diffInHours(new Carbon($this->updated_at)) < 24;
    }

    /*
     * Accessors and Mutators
     */

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->format('m-d H:i');
    }

	
}
