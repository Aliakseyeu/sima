<?php

namespace App\Repositories;

use Ixudra\Curl\Facades\Curl;
use App\Objects\{Delivery};
use App\Exceptions\NotFoundException;

class DeliveryRepository extends Repository
{

    public function getDeliveryPrice(int $id, int $qty): Delivery {
        $delivery = Curl::to($this->getDeliveryPriceUrl())
            // ->withData($this->getDeliveryData($this->getDeliveryUser()->settlement_id, $id, $qty))
            ->withData($this->getDeliveryData(193824312, $id, $qty))
            ->asJson()
            ->post();
        return new Delivery($delivery);
    }

    protected function getDeliveryData(int $settlement, int $id, int $qty): array {
        return [
            'settlement_id' => $settlement,
            'items' => ['item_id' => $id, 'qty' => $qty]
        ];
    }

    protected function getDeliveryPriceUrl(): string {
        return config('api.url').'/delivery-price/';
    }

    public function getDeliveryUser() {
        $user = Curl::to($this->getDeliveryAddressUrl())
            ->withContentType('application/json')
            ->withOption('USERPWD', config('api.login').':'.config('api.pass'))
            ->asJson()
            ->get();
        try {
            dd($user);
            return $user->items[0];
        } catch (\Exception $e){
            throw new NotFoundException('user');
        }
    }

    protected function getDeliveryAddressUrl(): string {
        return config('api.url').'/user-delivery-address/';
    }

}
