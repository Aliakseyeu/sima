@extends('layouts.app')

@section('body')
Заказ № {{ $group->id }}
<div>
    Курс
    <input type="text" name="rate" class="report-rate">
</div>

<table class="report-table table table-sm table-borderless table-striped">
    <tr>
        <th>#</th>
        <th>Заказчик</th>
        <th>Название</th>
        <th>Цена</th>
        <th>Количество</th>
        <th>Доставка</th>
        <th>Сумма </th>
        <th>Итого</th>
        <th>Общая сумма</th>
    </tr>
    @php $totalSum = 0 @endphp
    @foreach($report->getUsers() as $user)
        @php $userLoop = $loop @endphp
        @php $total = 0 @endphp
        @foreach($user->getOrders() as $order)
            @php $pivot = $order->user($user->getUser())->pivot @endphp
            <tr>
                <td>@if($loop->iteration == 1) {{ $userLoop->iteration }} @endif</td>
                <td>@if($loop->iteration == 1) {{ $user->getUser()->name }} {{ $user->getUser()->surname }} @endif</td>
                @if(!$order->item->empty())
                    <td>
                        {{ str_limit($order->item->name, $limit = 50, $end = '...') }}
                    </td>
                    <td>{{ $order->item->price }}</td>
                    <td>{{ $pivot->qty }}</td>
                    <td>{{ $pivot->delivery->getPrice() }}</td>
                    @php $sum = $order->item->price * $pivot->qty + (float)$pivot->delivery->getPrice() @endphp
                    @php $total += $sum @endphp
                    <td>{{ $sum }}</td>
                    <td class="userSum">
                        @if($loop->iteration == $loop->count)
                            <div class='ru'>{{ $total }}</div>
                            <div class='by'></div>
                            @php $totalSum += $total @endphp
                        @endif
                    </td>
                    <td class="totalSum">
                        @if($userLoop->iteration == $userLoop->count && $loop->iteration == $loop->count)
                            <div class="ru">{{ $totalSum }}</div>
                            <div class="by"></div>
                        @endif
                    </td>
                @else
                    <td colspan=7>Информация о товаре отсутствует</td>
                @endif
            </tr>
        @endforeach
    @endforeach
</table>
@endsection