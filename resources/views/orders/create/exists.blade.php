@extends('orders.create.order')

@section('store-form')

    <form action='/order/store_exists' method='post'>
        @csrf
        <input type="hidden" name="id" id='id' value="{{ $item->order->id }}">
        <input type="hidden" name="page" id='page' value="{{ $page }}">
        <div class="form-group">
            <label for="qty">Количество</label>
            <input type="text" name='qty' class="form-control" id="qty" aria-describedby="qty" placeholder="Количество" required>
            <div id="item-result"></div>
        </div>
        <div class="form-group">
            <button class='btn btn-primary find-article' type="submit">Добавить</button>
        </div>
    </form>

@endsection



