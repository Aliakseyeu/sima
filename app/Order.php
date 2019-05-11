<?php

namespace App;

use Event;
use App\Objects\Delivery;
use App\Exceptions\BaseException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;

class Order extends BaseModel
{

    use \App\Traits\OrderUser;

    protected $fillable = ['group_id'];

    /*

    public function findOrderUser(int $id): Builder
    {
        return $this->users()->find($id);
    }

    public function getPivotDeliveryInfoAttribute($value): Delivery
    {
        return new Delivery(json_decode($this->pivot->delivery_info));
    }

    public function getByArticle(int $article)
    {
        return $this->getAllBy('article', $article, 'article');
    }

    public function user(User $user)
    {
        return $this->users->where('id', $user->id)->first();
    }*/

    /*
     * Queries
     */

    public function findUser(int $user): ?User
    {
        return $this->users()->whereUserId($user)->first();
    }

    public function attachUser(int $user, array $data): bool
    {
        try {
            $this->users()->attach($user, $data);
            if($this->findUser($user)){
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw new BaseException();
        }
    }

    public function findOrderUserBuilder(int $id): Builder
    {
        return $this->users()->newPivotStatement()->whereId($id);
    }
    
    /*
     * Fill data
     */

    public function getFillData(int $group, int $item): array
    {
        return [
            'group_id' => $group,
        ];
    }

    /*
     * Logging
     */

    public static function boot()
    {
        parent::boot();
        static::created(function ($order) {
            Event::fire('order.created', $order);
        });
    }

    /*
     * Relationships
     */

    public function users()
    {
        return $this->belongsToMany('App\User', 'order_user')->withPivot('id', 'qty', 'delivery', 'delivery_info')->withTimestamps();
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    public function item()
    {
        return $this->hasOne(Item::class);
    }

    /*
     * Accessors and Mutators
     */

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->format('m-d H:i');
    }



}
