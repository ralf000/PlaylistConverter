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
            @if (session('status') && is_array(session('status')))
                <ul>
                    @foreach(session('status') as $status)
                        <li>{{ $status }}</li>
                    @endforeach
                </ul>
            @else
                {{ session('status') }}
            @endif
        </div>
    </div>
@endif
{{--/Сообщения--}}

{{--Сообщения--}}
@if (session('info'))
    <div class="panel-body">
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    </div>
@endif
{{--/Сообщения--}}