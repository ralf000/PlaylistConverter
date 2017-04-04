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


    <div class="panel-body">
        <button type="submit" class="btn btn-primary" form="groups-form">
            Сохранить
        </button>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-group">
            Добавить группу
        </button>
        <a href="{{ route('sort-groups') }}" class="btn btn-warning">
            Сортировать группы
        </a>
        <button type="button" class="btn btn-default pull-right" id="update-groups" data-token="{{ csrf_token() }}">
            Обновить список групп из текущего плейлиста
        </button>
    </div>
    <div class="panel-body">
        <div class="col-md-12">
            @if(!empty($groups) && is_array($groups))
                <form class="form-horizontal" role="form" action="{{ route('groups-update') }}" method="post"
                      id="groups-form">
                    {{ csrf_field() }}
                    @foreach ($groups as $group)
                        <input type="hidden" class="id-input" name="{{$group['id']}}[id]" value="{{$group['id']}}">
                        <input type="hidden" name="{{$group['id']}}[original_name]" value="{{$group['original_name']}}">

                        <div class="form-group">
                            <span style="color: gray;">Оригинальное название: {{$group['original_name']}}</span>
                            <div class="input-group">
                                @if($group['hidden'] === 0)
                                <input name="{{$group['id']}}[new_name]"
                                       type="text" class="form-control"
                                       id="{{$group['id']}}"
                                       placeholder="{{$group['new_name']}}"
                                       value="{{$group['new_name']}}">
                                @else
                                    <input name="{{$group['id']}}[new_name]"
                                           type="text" class="form-control"
                                           id="{{$group['id']}}"
                                           placeholder="{{$group['new_name']}}"
                                           style="opacity: 0.4"
                                           value="{{$group['new_name']}}" disabled>
                                @endif
                            <span class="input-group-btn">
                                @if($group['hidden'] === 0)
                                    <button data-id="{{$group['id']}}"
                                            data-token="{{ csrf_token() }}"
                                            class="change-visibility-btn element-hide-btn btn btn-default"
                                            type="button">
                                    Скрыть
                                    </button>
                                @else
                                    <button data-id="{{$group['id']}}"
                                            data-token="{{ csrf_token() }}"
                                            class="change-visibility-btn element-show-btn btn btn-default"
                                            type="button">
                                    Показать
                                    </button>
                                @endif
                                <button data-id="{{$group['id']}}"
                                        data-element-name="{{$group['new_name']}}"
                                        class="element-delete-btn btn btn-default"
                                        type="button">
                                <span class="glyphicon glyphicon-remove"></span>
                                </button>
                            </span>
                            </div>
                        </div>
                    @endforeach
                </form>

                {{--Форма для удаления группы--}}
                <form action="{{ route('groups-delete') }}" method="post" id="element-delete">
                    {{ csrf_field() }}
                    {{ method_field('delete') }}
                    <input type="hidden" name="id">
                </form>
                {{--/Форма для удаления группы--}}

                {{--Форма для сокрытия группы--}}
                <form action="{{ route('change-group-visibility') }}" method="post" id="change-visibility">
                    {{ csrf_field() }}
                    <input type="hidden" name="id">
                </form>
                {{--/Форма для сокрытия группы--}}

            @else
                <p>Группы пока не добавлены</p>
            @endif
        </div>
    </div>
</div>
@include('admin.include.add-group-modal')
<script src="{{ asset('/assets/js/admin/groups.js') }}"></script>