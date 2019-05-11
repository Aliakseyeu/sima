<td>{{ $loop->iteration }}</td>
<td>
    <a href='https://sima-land.ru{{ $order->item->itemUrl }}'>
        <img src="{{ $order->item->img }}" alt="{{ $order->item->name }}" title="{{ $order->item->name }}">
    </a>
</td>
<td>
    <a href='https://sima-land.ru{{ $order->item->itemUrl }}'>{{ $order->item->name }}</a><br>
    Артикул: {{ $order->item->sid }}
</td>