@extends('index')

@section('orders')
    @if($groups->count() <= 0)
        <div class="alert alert-danger" role="alert">
            Групп нет
        </div>
    @else
        @yield('list')
    @endif

    {{ $groups->links() }}
@endsection