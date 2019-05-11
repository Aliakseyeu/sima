@extends('layouts.master')

@section('content')

    @if(isset($user))
        <form action='{{ url('/user/update') }}' method='post'>
            @csrf
            <input type="hidden" name="id" value='{{ $user->id }}'>
            <div class="form-group">
                <label for="name">Имя</label>
                <input type="text" class="form-control" name='name' id="name" placeholder="Имя" value="{{ $user->name }}">
            </div>
            <div class="form-group">
                <label for="surname">Фамилия</label>
                <input type="text" class="form-control" name='surname' id="surname" placeholder="Фамилия" value="{{ $user->surname }}">
            </div>
            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="text" class="form-control" name='phone' id="phone" placeholder="Телефон" value="{{ $user->phone }}">
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    @endif

@endsection
