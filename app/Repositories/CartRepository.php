<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 29.10.18
 * Time: 21:45
 */

namespace App\Repositories;

use Ixudra\Curl\Facades\Curl;

class CartRepository
{

    public function send(array $data)
    {
        $data1 = Curl::to($this->getUrl())
            ->withOption('USERPWD', $this->getAuth())
            ->withData($data)
            ->asJson()
            ->post();
//        if(is_array($data1)){
//            dd($data, $data1);
//        }
        return $data1;
    }

    protected function getUrl(): string
    {
        return config('api.url').'/cart-item/';
    }

    protected function getAuth(): string
    {
        return config('api.login').':'.config('api.pass');
    }

    public function getPutData(int $id, int $sid, int $qty): array
    {
        return [
            'item_id' => $id,
            'item_sid' => $sid,
            'qty' => $qty
        ];
    }

}