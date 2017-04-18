<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">{{ $title }}</div>
    </div>

    @include('admin.include.errors&messages')

    <div class="panel-body">
        <button type="submit" class="btn btn-primary" form="channels-form">
            Сохранить
        </button>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-channel">
            Добавить канал
        </button>
        <button type="button" class="btn btn-default pull-right" id="update-from-playlist">
            Синхронизировать с плейлистом
        </button>
    </div>
    <div class="panel-body">
        <div class="col-md-12">
            @if(!empty($channels) && is_array($channels))
                <form class="form-horizontal" role="form" action="{{ route('channels-update') }}" method="post"
                      id="channels-form">
                    {{ csrf_field() }}
                    @foreach ($groups as $group)
                        <div class="form-group">
                            <div class="input-group">
                                        <span class="input-group-btn">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                               href="#{{$group['id']}}"
                                               aria-expanded="true" aria-controls="{{$group['id']}}"
                                               class="btn btn-default accordion-title collapsed">
                                            <span class="glyphicon glyphicon-arrow-down"></span>
                                        </a>
                                        </span>
                                <input name="group[{{$group['id']}}][new_name]"
                                       type="text"
                                       class="form-control"
                                       placeholder="{{$group['new_name']}}"
                                       {!! ($group['hidden'] !== 0) ? 'style="opacity: 0.4;"' : ''!!}
                                       value="{{$group['new_name']}}"
                                        {{($group['hidden'] !== 0) ? 'disabled="disabled"' : ''}}>
                            </div>
                        </div>
                        <div class="row">
                            <div id="{{$group['id']}}"
                                 class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby=heading-group-{{$group['id']}}>
                                <table cellpadding="0"
                                       cellspacing="0"
                                       border="0"
                                       class="table table-striped"
                                       id="example">
                                    <thead>
                                    <tr>
                                        <th class="narrow-col"> </th>
                                        {{--<th>Оригинальное название</th>--}}
                                        <th>Название</th>
                                        <th>Группа</th>
                                        <th>Ссылка</th>
                                        <th style="width: 10%">Скрыть</th>
                                        <th class="narrow-col">Удалить</th>
                                    </tr>
                                    </thead>
                                    <tbody class="sortable">
                                    @foreach($channels as $channel)
                                        @if($channel['group_id'] === $group['id'])
                                            @include('admin.include.channel')
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
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

                {{--Форма для обновления списка каналов и групп из плейлиста--}}
                <form action="{{ route('update-from-playlist') }}" method="post" id="update-from-playlist-form">
                    {{ csrf_field() }}
                </form>
                {{--/Форма для обновления списка каналов и групп из плейлиста--}}

            @else
                <p>Каналы пока не добавлены</p>
            @endif
        </div>
    </div>
</div>
@include('admin.include.add-channel-modal')

<script src="{{ asset('/assets/js/admin/channels.js') }}"></script>