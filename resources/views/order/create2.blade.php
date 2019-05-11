@extends('layouts.master')

@section('content')

<h1 class="display-2">Создать заказ</h1>

<form action='/order/store' method='post' id='create-order'>
    @csrf
    <input type="hidden" name="group" id='group' value="{{ $group }}">
	<div class="form-group">
		<label for="article">Артикул</label>
		<input type="text" name='article' class="form-control" id="article" aria-describedby="article" placeholder="Артикул">
        <div id="item-result"></div>
	</div>
	<div class="form-group">
	    <button class='btn btn-primary find-article'>Найти</button>
	</div>
	<div class="form-group">
		<label for="qty">Количество</label>
		<input type="text" name='qty' class="form-control" id="qty" placeholder="Количество">
	</div>
	<div class="form-group">
	    <button type="submit" class="btn btn-primary">Заказать</button>
	</div>
</form>

@endsection
