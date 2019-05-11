<?php

namespace Tests\Feature\Repositories;

use DB;

class OrderRepository
{
    
    protected $model;

    public function find(int $id): void
    {
        $this->model = DB::table('order_user')->whereId($id)->get();
    }

    public function restore(): void
    {
        dd($this->model);
        DB::table('order_user')->whereId($this->model->id)->update();
    }

    public function set($model): object
    {
        $this->model = $model;
    }

    public function get(): Order
    {
        return $this->order;
    }

}
