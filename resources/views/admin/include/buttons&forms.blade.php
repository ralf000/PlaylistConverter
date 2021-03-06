<div class="panel-body">
    <button type="submit" class="btn btn-primary" form="channels-form">
        Сохранить
    </button>
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-channel">
        <span class="glyphicon glyphicon-plus"></span> Канал
    </button>
    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#add-group">
        <span class="glyphicon glyphicon-plus"></span> Группа
    </button>
    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#add-playlist">
        <span class="glyphicon glyphicon-plus"></span> Плейлист
    </button>
        <button type="button" class="btn btn-default pull-right" id="reset-playlist">
            <span class="glyphicon glyphicon-remove"></span> Сбросить все данные из плейлиста
        </button>
    @if (!\App\Config::get('builderMode'))
        <button type="button" class="btn btn-default pull-right" id="update-from-playlist">
            <span class="glyphicon glyphicon-refresh"></span> Синхронизировать с плейлистом
        </button>
    @endif
</div>

{{--Форма для удаления канала--}}
<form action="{{ route('channel-delete') }}" method="post" id="channel-delete">
    {{ csrf_field() }}
    {{ method_field('delete') }}
    <input type="hidden" name="id">
</form>
{{--/Форма для удаления канала--}}

{{--Форма для удаления группы--}}
<form action="{{ route('groups-delete') }}" method="post" id="group-delete">
    {{ csrf_field() }}
    {{ method_field('delete') }}
    <input type="hidden" name="id">
</form>
{{--/Форма для удаления группы--}}

{{--Форма для сокрытия канала--}}
<form action="{{ route('change-channel-visibility') }}" method="post" id="change-channel-visibility">
    {{ csrf_field() }}
    <input type="hidden" name="id">
</form>
{{--/Форма для сокрытия канала--}}

{{--Форма для сокрытия группы--}}
<form action="{{ route('change-group-visibility') }}" method="post" id="change-group-visibility">
    {{ csrf_field() }}
    <input type="hidden" name="id">
</form>
{{--/Форма для сокрытия группы--}}

{{--Форма для обновления списка каналов и групп из плейлиста--}}
<form action="{{ route('update-from-playlist') }}" method="post" id="update-from-playlist-form">
    {{ csrf_field() }}
</form>
{{--/Форма для обновления списка каналов и групп из плейлиста--}}

{{--Форма для сброса всех данных из плейлиста--}}
<form action="{{ route('reset-playlist') }}" method="post" id="reset-playlist-form">
    {{ csrf_field() }}
</form>
{{--/Форма для сброса всех данных из плейлиста--}}
