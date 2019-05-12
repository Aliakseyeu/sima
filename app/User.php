<?php

namespace App;

use App\Objects\Delivery;
use App\Exceptions\NotAuthorizedException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;
    use \App\Traits\OrderUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'phone', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /*
     * Queries
     */

    public function findOrderUserBuilder(int $id): Builder
    {
        return $this->orders()->newPivotStatement()->whereId($id);
    }

    public function pivotIsNew(): bool {
        return Carbon::now()->diffInHours(new Carbon($this->pivot->updated_at)) < 24;
    }

    /*
     * ----------
     */

    /*public function getByPivotId(int $id) {
        return $this->whereHas('orders', function($q) use ($id){
            return $q->where('order_user.id', $id);
        })->get()->first();
        try {
            return $this->orders()->whereHas('orders', function($q) use ($id){
                return $q->where('order_user.id', $id);
            });
        } catch (\Exception $e){
            throw new NotAuthorizedException();
        }
    }

    public function detachOrder()
    {
        try {
            return $this->orders()->detach($this->id);
        } catch (\Exception $e){
            throw new NotAuthorizedException();
        }
    }*/

    /*
     * Accessors and mutators
     */

    public function getFullNameAttribute(){
        return $this->surname . ' ' . $this->name;
    }

    public function getPivotDeliveryInfoAttribute($value): Delivery {
        return new Delivery(json_decode($this->pivot->delivery_info));
    }

    // public function setPasswordAttribute($value) {
    //     $this->attributes['password'] = Hash::make($value);
    // }

    /*
     * Relationships
     */

    public function orders(){
        return $this->belongsToMany('App\Order', 'order_user')->withPivot('id', 'qty', 'delivery', 'delivery_info')->withTimestamps();
    }

    public function ordersByGroup(int $group)
    {
        return $this->belongsToMany('App\Order', 'order_user')
            ->withPivot('id', 'qty', 'delivery', 'delivery_info')
            ->withTimestamps()
            ->whereHas('group', function($q) use ($group){
                return $q->whereId($group);
            });
    }

    public function groups(){
        return $this->hasMany('App\Group');
    }

    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    /*
     * Roles
     */

    public function hasRole($role){
        return in_array($role, array_pluck($this->roles->toArray(), 'slug'));
    }

    public function isAdmin(){
        return $this->hasRole('admin');
    }

}
