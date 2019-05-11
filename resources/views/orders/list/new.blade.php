@extends('orders.list.list')

@section('list')
    @foreach($groups as $group)

        <div class="group">

            <div class="">
                @if(Auth::user() && Auth::user()->isAdmin())
                    <a href="{{ url('/group/destroy/'.$group->id) }}" class="btn btn-danger float-right mb-3 mr-3" title="Для удаления группы в ней не должно быть заказов.">Удалить группу</a>
                @endif
                @include('orders.list.group-info', compact('group'))
                <a href="{{ url('/item/create/'.$group->id.'?page='.Request::get('page', 1)) }}" class="btn btn-success float-right mb-3 mr-3">Сделать заказ</a>
                <div class="clearfix"></div>
            </div>

            @if($group->orders->count() > 0)

                <table class="table">
                    @include('orders.list.header')
                    @foreach($group->orders as $order)
                        @if(!$order->item)
                            @continue
                        @endif
                        @php $addForm = true; @endphp
                        <tr>
                            @include('orders.list.item-info', compact('loop', 'order'))
                            <td>
                                @foreach($order->users as $user)
                                    @include('orders.list.user-info', compact('order', 'user'))
                                    <a href="{{ url('/delivery/update/'.$user->pivot->id) }}" class="btn btn-sm btn-outline-{{ $user->pivotIsNew() ? 'success' : 'danger' }}">
                                        {{ $user->pivotDeliveryInfo->getPrice() }}
                                    </a>
                                    @if($user->id == Auth::id() || (Auth::user() && Auth::user()->isAdmin()))
                                        <span>
                                            <a href="#" data-id="{{ $user->pivot->id }}" class="order-edit" title='Редактировать'><i class="fa fa-pen"></i></a>
                                            <a href="{{ url('/order/destroy/'.$user->pivot->id) }}" title='Удалить'><i class="fa fa-trash"></i></a>
                                        </span>
                                    @endif
                                    @if($user->id == Auth::id())
                                        @php $addForm = false; @endphp
                                    @endif
                                    <br>
                                @endforeach
                                @include('orders.list.total', compact('order'))
                                <form action='/order/store_exists' method='POST' class='form-inline'>
                                    @csrf
                                    <input type='hidden' name='id' value='{{ $order->id }}'>
                                    <input type='hidden' name='page' value='{{ Request::get('page', 1) }}'>
                                    <input type='text' class="form-control form-control-sm" name='qty' placeholder='Количество' required>
                                    <button class='btn btn-primary btn-sm' type='submit'>Добавить</button>
                                </form>
                            </td>
                            <td>
                                <a href="{{ url('/item/update/'.$order->item->id) }}" class="btn btn-sm btn-outline-{{ $order->item->isNew() ? 'success' : 'danger' }}">
                                    {{ $order->item->price }} {{ $order->item->currency }}
                                </a>
                            </td>
                            @include('orders.list.item-added-info', compact('order'))
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Пожалуйста, во избежание конфликтов, добавляте заказ в пустую корзину!</td>
                        <td colspan=2>
                            @if(Auth::user() && Auth::user()->isAdmin())
                                <a href="{{ url('/group/toCart/'.$groups->items()[0]->id) }}" class="btn btn-success">В корзину</a>
                            @endif
                        </td>
                        <td>
                            @if(Auth::user() && Auth::user()->isAdmin())
                                <a href="{{ url('/archive/store/'.$groups->items()[0]->id) }}" class="btn btn-danger" onclick="if(!confirm('Вы уверены что хотите перемесить заказ в архив?')){return false;}">В архив</a>
                            @endif
                        </td>
                    </tr>
                </table>

            @else

                @include('orders.list.no-orders')

            @endif

        </div>

    @endforeach

@endsection



