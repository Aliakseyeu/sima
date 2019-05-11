@extends('orders.create.master')

@section('data')

    <table class="table item-info">
        <tr>
            <th>Изображение</th>
            <th>Название</th>
            <th>Заказчики (количество + доставка)</th>
            <th>Цена</th>
            <th>Минимум</th>
        </tr>
        <tr>
            <td><a href='https://sima-land.ru{{ $item->itemUrl }}'><img src="{{ $item->img }}" alt="{{ $item->name }}" title="{{ $item->name }}"></a></td>
            <td>
                <a href='https://sima-land.ru{{ $item->itemUrl }}'>{{ $item->name }}</a><br>
                Артикул: {{ $item->sid }}
            </td>
            <td>
                @if(!empty($users))
                    @php $sum = 0 @endphp
                    @foreach($users as $user)
                        <div>{{ $user->surname }} {{ $user->name }} : {{ $user->pivot->qty }}{{ $item->pluralNameFormat }}</div>
                        @php $sum += $user->pivot->qty @endphp
                    @endforeach
                    <div class="alert alert-{{ $sum >= $item->min_qty ? 'success' : 'danger' }}">
                        Итого - {{ $sum }}
                    </div>
                @endif
            </td>
            <td>{{ $item->price }}<br>{{ $item->currency }}</td>
            <td>{{ $item->min_qty }}{{ $item->pluralNameFormat }}</td>
        </tr>
    </table>

    @yield('store-form')

@endsection
