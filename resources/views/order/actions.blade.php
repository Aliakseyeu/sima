@if(Auth::user() && Auth::user()->isAdmin())
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Пожалуйста, во избежание конфликтов, добавляте заказ в пустую корзину!</td>
        <td colspan=2><a href="{{ url('/order11/toCart/'.$group->id) }}" class="btn btn-success">В корзину</a></td>
        <td><a href="{{ url('/order/toArchive/'.$group->id) }}" class="btn btn-danger" onclick="if(!confirm('Вы уверены что хотите перемесить заказ в архив?')){return false;}">В архив</a></td>
    </tr>
@endif