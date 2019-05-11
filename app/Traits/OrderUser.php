<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 17.10.18
 * Time: 21:14
 */

namespace App\Traits;

use App\Exceptions\BaseException;
use App\Exceptions\NotFoundException;
use App\Objects\Delivery;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;

trait OrderUser
{

    public function updatePivotByBuilder(Builder $pivot, array $data): bool
    {
        try {
            return $pivot->update($data);
        } catch (\Exception $e) {
            throw new BaseException();
        }
    }

    /**
     * @param int $qty
     * @param Delivery $delivery
     * @return array
     */
    public function getOrderUserData(int $qty, Delivery $delivery): array
    {
        return [
            'qty' => $qty,
            'delivery' => $delivery->getPrice(),
            'delivery_info' => $delivery,
            'updated_at' => Carbon::now()
        ];
    }

    public function findOrderUserBuilderOrException(int $id): Builder
    {
        $pivot = $this->findOrderUserBuilder($id);
        if(!$pivot->first()){
            throw new NotFoundException(__('messages.order'));
        }
        return $pivot;
    }

    abstract public function findOrderUserBuilder(int $id): Builder;

}