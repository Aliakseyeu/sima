@extends('layouts.master')

@section('content')

@if(!empty($news))
    <div class="alert alert-info" role="alert">
        <ul>
            @foreach($news as $new)
                <li>{{ $new->text }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h1 class="display-2">Заказы!</h1>

@yield('addTitle')

<a href="/" class="btn btn-primary float-right mb-3">Актуальные</a>
<a href="{{ url('/archive') }}" class="btn btn-secondary float-right mb-3 mr-3">Архивные</a>
@if(Auth::id() && Auth::user()->isAdmin())
    <a href="{{ url('/group/store') }}" class="btn btn-warning float-right mb-3 mr-3">Создать группу</a>
@endif
<div class="clearfix"></div>

@yield('orders')

@endsection
