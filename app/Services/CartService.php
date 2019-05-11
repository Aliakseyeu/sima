<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Objects\Response\IResponsable;
use App\Order;
use App\Repositories\CartRepository;
use Illuminate\Support\Collection;

class CartService extends Service
{

    protected $cartRepository;
    protected $response;

    public function __construct(CartRepository $cartRepository, IResponsable $response)
    {
        $this->cartRepository = $cartRepository;
        $this->response = $response;
    }

    public function put(Collection $orders): IResponsable
    {
        $this->isAuthorized();
        $this->isOrdersExists($orders);
        foreach($orders as $order){
            if(!$this->isItemInfoExists($order)){
                continue;
            }

            $qty = $this->getQty($order);
            if(!$this->isQtyMoreThanMin($order, $qty)){
                continue;
            }

            $result = $this->cartRepository->send(
                $this->cartRepository->getPutData($order->item->pid, $order->item->sid, $qty)
            );
            if(!$this->checkResult($order, $result)){
                continue;
            }
            $this->checkAddedQty($order, $qty, $result);
        }
        return $this->response;
    }

    protected function isOrdersExists(Collection $orders): bool
    {
        if($orders->count() <= 0){
            throw new NotFoundException('order');
        }
        return true;
    }

    protected function isItemInfoExists(Order $order): bool
    {
        if(!$order->item){
            $this->response->addError('Пропущен неизвестный товар с артикулом. Пожалуйста сверьтесь с удаленной корзиной.');
            return false;
        }
        return true;
    }

    protected function getQty(Order $order): int
    {
        return $order->users->reduce(function($carry, $user){
            return $carry + $user->pivot->qty;
        });
    }

    protected function isQtyMoreThanMin(Order $order, int $qty): bool
    {
        if($qty < $order->item->min_qty){
            $this->response->addError('Товар с артикулом '.$order->item->sid.' не добавлен в корзину, т.к. количество меньше минимального');
            return false;
        }
        return true;
    }

    protected function checkResult(Order $order, $result): bool
    {
        if (is_object($result) && !empty($result->id)) {
            $this->response->addSuccess('Товар с артикулом ' . $order->item->sid . ' в количестве ' . $result->qty . ' ' . $order->item->pluralNameFormat . ' успешно добавлен');
            return true;
        } else {
            $this->response->addError('Товар с артикулом ' . $order->item->sid . ' не добавлен в корзину. Пожалуйста добавьте в ручную.');
            return false;
        }
    }

    protected function checkAddedQty(Order $order, int $qty, object $result): void
    {
        if ($qty != $result->qty) {
            $this->response->addError('Количество выбранного товара с артикулом ' . $order->item->sid . ' отличается от количества добавленного в корзину. Пожалуйста проверьте.');
        }
    }
    
}
