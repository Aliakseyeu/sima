<div class="float-left pt-2"><b>Группа №{{ $group->id }}. Создана {{ $group->created_at }}</b></div>
<a href="{{ url('/report/show/'.$group->id) }}" class="btn btn-dark float-right mb-3 mr-3">Сделать отчет</a>