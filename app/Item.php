<?php

namespace App;

use App\Exceptions\BaseException;
use App\Objects\Item as ItemObject;
use Carbon\Carbon;


class Item extends BaseModel
{

    protected $guarded = ['id'];
    protected $fillable = ['order_id','pid', 'sid', 'info', 'updated_at'];
    protected $itemObject;

    /*
     * Queries
     */

    /**
     * @param int $group
     * @param int $sid
     * @return Item|null
     * @throws BaseException
     */
    public function getByGroupAndSid(int $group, int $sid): ?Item
    {
        try {
            return $this->whereSid($sid)->whereHas('order', function ($q) use ($group) {
                return $q->whereHas('group', function ($q) use ($group) {
                    return $q->whereId($group);
                });
            })->first();
        } catch (\Exception $e) {
            throw new BaseException();
        }
    }

    /*
     * Fill data
     */

    public function getUpdateData(ItemObject $item): array
    {
        return [
            'sid' => $item->sid,
            'info' => $item,
            'updated_at' => Carbon::now(),
        ];
    }

    public function getFillData(ItemObject $item): array
    {
        return array_merge([
                'pid' => $item->id,
            ],
            $this->getUpdateData($item)
        );
    }

    /*
     * Relationships
     */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /*
     * Accessors and Mutators
     */

    public function getInfoAttribute($value): ItemObject
    {
        if (is_null($this->itemObject)) {
            $this->itemObject = new ItemObject(json_decode($value));
        }
        return $this->itemObject;
    }

    /*
     * Override
     */

    public function __get($key)
    {
        return parent::__get($key) ?? parent::__get('info')->$key;
    }

}