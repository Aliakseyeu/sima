<div class="float-left pt-2">
    <b>Группа №{{ $group->id }}. Создана {{ $group->created_at }}</b>
    @if(Auth::user()->isAdmin())
        <a href='/group/{{ $group->id }}/show/?page={{ Request::get('page') }}' title='Редактировать' ><i class="fa fa-pen"></i></a>
    @endif
    {{ $group->comment }}
</div>
<a href="{{ url('/report/show/'.$group->id) }}" class="btn btn-dark float-right mb-3 mr-3">Сделать отчет</a>