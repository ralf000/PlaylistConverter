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
        <a href="{{route('groups')}}" class="btn btn-default">
            Назад
        </a>
    </div>
    <div class="panel-body">
        @if(!empty($groups) && is_array($groups))
            <ul id="sortable" class="list-group">
                @foreach ($groups as $group)
                    <li class="ui-state-default list-group-item">{{$group['new_name']}} <span
                                class="glyphicon glyphicon-sort pull-right"></span></li>
                @endforeach
            </ul>
        @else
            <p>Группы пока не добавлены</p>
        @endif
    </div>
</div>
<script src="{{ asset('/assets/js/admin/groups.js') }}"></script>