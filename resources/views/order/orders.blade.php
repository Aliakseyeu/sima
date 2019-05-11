@extends('index')

@section('orders')
    @if($groups->count() <= 0)
        <div class="alert alert-danger" role="alert">
            Групп нет
        </div>
    @else
        @foreach($groups as $group)

            <div class="group">

                <div class="">
                    <div class="float-left pt-2"><b>Группа №{{ $group->id }}. Создана {{ $group->created_at }}</b></div>
                    <a href="{{ url('/order/report/'.$group->id) }}" class="btn btn-dark float-right mb-3 mr-3">Сделать отчет</a>
                    <a href="{{ url('/item/create/'.$group->id.'/?page=') }}" class="btn btn-success float-right mb-3 mr-3">Сделать заказ</a>
                    <div class="clearfix"></div>
                </div>

                @if($group->orders->count() > 0) 

                    <table class="table">
                        <tr>
                            <th>#</th>
                            <th>Изображение</th>
                            <th>Название</th>
                            <th>Заказчики (количество + доставка)</th>
                            <th>Цена</th>
                            <th>Минимум</th>
                            <th>Создан</th>
                        </tr>
                        @yield('archivedInfo')
                        @foreach($group->orders as $order)
                            @php $addForm = true; @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href='https://sima-land.ru{{ $order->item->itemUrl }}'>
                                        <img src="{{ $order->item->img }}" alt="{{ $order->item->name }}" title="{{ $order->item->name }}">
                                    </a>
                                </td>
                                <td>
                                    <a href='https://sima-land.ru{{ $order->item->itemUrl }}'>{{ $order->item->name }}</a><br>
                                    Артикул: {{ $order->article }}
                                </td>
                                <td>
                                    @foreach($order->users as $user)
                                        {{ $user->full_name }} : <span class='qty'>{{ $user->pivot->qty }}</span>{{ $order->item->pluralNameFormat }} +
                                        <a href="{{ url('/delivery/edit/'.$user->pivot->id) }}" class="btn btn-sm btn-outline-{{ $user->pivotIsNew() ? 'success' : 'danger' }}">
                                            {{ $user->pivotDeliveryInfo->getPrice() }}
                                        </a>
                                        @if($user->id == Auth::id() || (Auth::user() && Auth::user()->isAdmin()))
                                            <span>
                                                <a href="#" data-id="{{ $user->pivot->id }}" class="order-edit" title='Редактировать'><i class="fa fa-pen"></i></a>
                                                <a href="{{ url('/order/delete/'.$user->pivot->id) }}" title='Удалить'><i class="fa fa-trash"></i></a>
                                            </span>
                                        @endif
                                        @if($user->id == Auth::id())
                                            @php $addForm = false; @endphp
                                        @endif
                                        <br>
                                    @endforeach
                                    <div class="alert alert-{{ $order->users()->sum('qty') >= $order->item->min_qty ? 'success' : 'danger' }}">
                                        Итого: {{ $order->users()->sum('qty') }}
                                    </div>
                                    @if(Request::path() == '/' && $addForm)
                                        <form action='/order/add' method='POST' class='form-inline'>
                                            @csrf
                                            <input type='hidden' name='orderId' value='{{ $order->id }}'>
                                            <input type='hidden' name='userId' value='{{ Auth::id() }}'>
                                            <input type='hidden' name='article' value='{{ $order->article }}'>
                                            <input type='hidden' name='group' value='{{ $group->id }}'>
                                            <input type='text' class="form-control form-control-sm" name='qty' placeholder='Количество' required>
                                            <button class='btn btn-primary btn-sm' type='submit'>Добавить</button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <div class="alert alert-{{ $order->item->isNew() ? 'success' : 'danger' }}" role="alert">
                                        {{ $order->item->price }} {{ $order->item->currency }}
                                    </div>
                                </td>
                                <td>{{ $order->item->min_qty }}{{ $order->item->pluralNameFormat }}</td>
                                <td>{{ $order->created_at }}</td>
                            </tr>
                        @endforeach
                        @if(Request::path() == '/')
                            @include('order.actions', compact('group'))
                        @endif
                    </table>

                @else

                    <div class="alert alert-danger" role="alert">
                        Заказов нет
                    </div>

                @endif

            </div>

        @endforeach
    @endif

    @yield('paginate')
@endsection