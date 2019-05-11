@if (Session::has('success') && is_array(Session::get('success')) && count(Session::get('success')) > 0)
    <?php $success = Session::pull('success'); ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <ol>
            @foreach($success as $s)
                <li> {{ $s }}</li>
            @endforeach
        </ol>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (false && Session::has('errors') && is_array(Session::get('errors')) && count(Session::get('errors')) > 0)
    <?php $errorsMessages = Session::pull('errors'); ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ol>
            @foreach($errorsMessages as $e)
                <li> {{ $e }}</li>
            @endforeach
        </ol>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (count($errors) > 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ol>
            @foreach($errors->all() as $error)
                <li> {{ $error }}</li>
            @endforeach
        </ol>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (Session::has('info') && is_array(Session::get('info')) && count(Session::get('info')) > 0)
    <?php $info = Session::pull('info'); ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <ol>
            @foreach($info as $i)
                <li> {{ $i }}</li>
            @endforeach
        </ol>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif