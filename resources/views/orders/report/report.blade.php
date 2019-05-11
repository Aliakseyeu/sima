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
            <th>Заказчик/Название</th>
            <th>Артикул</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Сумма</th>
            <th>Доставка</th>
            <th>Итого</th>
        </tr>
        @php $totalAll = 0 @endphp
        @foreach($report->getUsers() as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>--- {{ $user->fullName }}</td>
                <td>Позиций</td>
                <td>{{ $user->orders->count() }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @php $totalOrder = 0 @endphp
            @foreach($user->ordersByGroup($group->id)->get() as $order)
                <tr>
                    @if($order->item)
                        <td></td>
                        <td>
                            {{ str_limit($order->item->name, $limit = 50, $end = '...') }}
                        </td>
                        <td>{{ $order->item->sid }}</td>
                        <td>{{ $order->item->price }}</td>
                        <td>{{ $order->pivot->qty }}</td>
                        <td>{{ $sum = $order->item->price * $order->pivot->qty }}</td>
                        <td>{{ $order->pivot->delivery }}</td>
                        <td>{{ $total = $sum + (float)$order->pivot->delivery }}</td>
                        @php $totalOrder += $total @endphp
                    @else
                        <td colspan=7>Информация о товаре отсутствует</td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="5"></td>
                <td colspan="2">Итого:</td>
                <td class="userSum">
                    <div class='ru'>{{ $totalOrder }}</div>
                    <div class='by'></div>
                    @php $totalAll += $totalOrder @endphp
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="5"></td>
            <td colspan="2">К оплате</td>
            <td class="totalSum">
                <div class="ru">{{ $totalAll }}</div>
                <div class="by"></div>
            </td>
        </tr>
    </table>
@endsection