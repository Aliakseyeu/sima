@extends('layouts.master')

@section('content')

Группа №{{ $group->id }}<br>
Создана {{ $group->created_at }}

<hr>

<form action='/group/update' method='POST' class='form'>
    @csrf
    <input type='hidden' name='id' value='{{ $group->id }}'>
    <input type='hidden' name='page' value='{{ Request::get('page', 1) }}'>
    <div class="form-group">
        <label for="comment">Комментарий</label>
        <input type='text' class="form-control" name='comment' value='{{ $group->comment }}' placeholder='Комментарий'>
      </div>
    <button class='btn btn-primary' type='submit'>Сохранить</button>
</form>

@endsection