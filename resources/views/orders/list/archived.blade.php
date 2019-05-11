@extends('orders.list.list')

@section('list')
        @foreach($groups as $group)

            <div class="group">

                <div class="">
                    @include('orders.list.group-info', compact('group'))
                    <div class="clearfix"></div>
                </div>

                @if($group->orders->count() > 0)

                    <table class="table">
                        @include('orders.list.header')
                        @foreach($group->orders as $order)
                            @if($order->item)
                                <tr>
                                    @include('orders.list.item-info', compact('loop', 'order'))
                                    <td>
                                        @foreach($order->users as $user)
                                            @include('orders.list.user-info', compact('order', 'user'))
                                            {{ $user->pivotDeliveryInfo->getPrice() }}
                                            <br>
                                        @endforeach
                                        @include('orders.list.total', compact('order'))
                                    </td>
                                    <td>
                                        {{ $order->item->price }} {{ $order->item->currency }}
                                    </td>
                                    @include('orders.list.item-added-info', compact('order'))
                                </tr>
                            @endif
                        @endforeach
                    </table>

                @else

                    @include('orders.list.no-orders')

                @endif

            </div>

        @endforeach
@endsection



