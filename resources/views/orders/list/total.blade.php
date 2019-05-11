<div class="alert alert-{{ $order->users()->sum('qty') >= $order->item->min_qty ? 'success' : 'danger' }}">
    Итого: {{ $order->users()->sum('qty') }}
</div>