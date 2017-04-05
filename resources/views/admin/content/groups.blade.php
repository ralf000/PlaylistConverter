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
    </div>
    <div class="panel-body">
        <div class="col-md-12">
            @if(!empty($groups) && is_array($groups))
                <form class="form-horizontal" role="form" action="{{ route('groups-update') }}" method="post"
                      id="groups-form">
                    {{ csrf_field() }}
                    <div id="sortable" class="groups-list">
                        @foreach ($groups as $group)
                            @include('admin.include.group')
                        @endforeach
                    </div>
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