{{--Ошибки--}}
@if (count($errors) > 0)
    <div class="panel-body">
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
{{--/Ошибки--}}

{{--Сообщения--}}
@if (session('status'))
    <div class="panel-body">
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    </div>
@endif
{{--/Сообщения--}}