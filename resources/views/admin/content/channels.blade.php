<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">{{ $title }}</div>
    </div>

    @include('admin.include.errors&messages')

    <div class="panel-body">
        <button type="submit" class="btn btn-primary" form="groups-form">
            Сохранить
        </button>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-group">
            Добавить канал
        </button>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                @if(!empty($channels) && is_array($channels))
                    <form class="form-horizontal" role="form" action="{{ route('groups-update') }}" method="post"
                          id="groups-form">
                        {{ csrf_field() }}
                        <div id="sortable" class="groups-list">
                            @foreach ($channels as $channel)
                                @include('admin.include.channel')
                            @endforeach
                        </div>
                    </form>

                    {{--Форма для удаления канала--}}
                    <form action="{{ route('channel-delete') }}" method="post" id="element-delete">
                        {{ csrf_field() }}
                        {{ method_field('delete') }}
                        <input type="hidden" name="id">
                    </form>
                    {{--/Форма для удаления канала--}}

                    {{--Форма для сокрытия канала--}}
                    <form action="{{ route('change-channel-visibility') }}" method="post" id="change-visibility">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                    </form>
                    {{--/Форма для сокрытия канала--}}

                @else
                    <p>Каналы пока не добавлены</p>
                @endif
            </div>
        </div>
    </div>
</div>
@include('admin.include.add-channel-modal')
<script src="{{ asset('/assets/js/admin/channels.js') }}"></script>