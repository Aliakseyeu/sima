<div>
    <form action='/order/change' method='POST' class='form-inline'>
        @csrf
        <input type='hidden' name='id' value='{{ $id }}'>
        <input type='text' class="form-control form-control-sm" name='qty' placeholder='Количество' required>
        <button class='btn btn-primary btn-sm' type='submit'>+</button>
    </form>
</div>