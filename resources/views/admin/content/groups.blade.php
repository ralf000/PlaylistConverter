<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">{{ $title }}</div>
    </div>

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


    <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#add-group">
        Добавить группу
    </button>
    <div class="panel-body">
        @if(!empty($groups) && is_array($groups))
            <form class="form-horizontal" role="form" action="{{ route('groups-update') }}" method="post">
                {{ csrf_field() }}
                @foreach ($groups as $group)
                    <input type="hidden" name="{{$group['id']}}[id]" value="{{$group['id']}}">

                    <div class="form-group">
                        <div class="input-group">
                            <input name="{{$group['id']}}[name]"
                                   type="text" class="form-control"
                                   id="{{$group['id']}}"
                                   placeholder="{{$group['name']}}"
                                   value="{{$group['name']}}">
                            <span class="input-group-btn">
                                <button data-id="{{$group['id']}}"
                                        data-element-name="{{$group['name']}}"
                                        class="element-delete-btn btn btn-default"
                                        type="button">
                                <span class="glyphicon glyphicon-remove"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                @endforeach
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>

            {{--Форма для удаления элемента--}}
            <form action="{{ route('groups-delete') }}" method="post" id="element-delete">
                {{ csrf_field() }}
                {{ method_field('delete') }}
                <input type="hidden" name="id">
            </form>
            {{--/Форма для удаления элемента--}}

        @else
            <p>Группы пока не добавлены</p>
        @endif
    </div>
</div>
@include('admin.include.add-group-modal')

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>