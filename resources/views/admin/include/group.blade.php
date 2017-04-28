<div class="sort-element group-block">

    <input type="hidden" class="id-input" name="group[{{$group['id']}}][id]"
           value="{{$group['id']}}">
    <input type="hidden" name="group[{{$group['id']}}][original_name]"
           class="original-name"
           value="{{$group['original_name']}}">
    <input type="hidden" name="group[{{$group['id']}}][sort]" class="sort"
           value="{{$group['sort']}}">
    <input type="hidden" name="group[{{$group['id']}}][disabled]" class="disable-tag"
           value="{{ $group['hidden'] }}">

    <div class="form-group">
        <div class="input-group">
        <span class="input-group-btn">
            <span class="btn btn-default glyphicon glyphicon-move sorting-btn"></span>
            <a role="button" data-toggle="collapse" data-parent="#accordion"
               href="#{{$group['id']}}" title="Развернуть"
               aria-expanded="true" aria-controls="{{$group['id']}}"
               class="btn btn-default accordion-title collapsed">
                <span class="glyphicon glyphicon-arrow-down"></span>
            </a>
        </span>
            <input name="group[{{$group['id']}}][new_name]"
                   type="text" class="form-control new-name"
                   placeholder="{{$group['new_name']}}"
                   {!! ($group['hidden'] !== 0) ? 'style="opacity: 0.4;"' : ''!!}
                   value="{{$group['new_name']}}"
                    {{($group['hidden'] !== 0) ? 'disabled="disabled"' : ''}}>
        <span class="input-group-btn">
            @if($group['hidden'] === 0)
                <button data-id="{{$group['id']}}"
                        data-token="{{ csrf_token() }}"
                        class="change-group-visibility-btn group-hide-btn btn btn-default"
                        type="button">Скрыть</button>
            @else
                <a data-id="{{$group['id']}}"
                   data-token="{{ csrf_token() }}"
                   class="change-group-visibility-btn group-show-btn btn btn-default"
                   type="button">Показать</a>
            @endif
            <button class="group-reset-btn btn btn-default"
                    title="Сбросить изменения">
                <span class="glyphicon glyphicon-share-alt"></span>
            </button>
            @if ($group['own']
            || in_array($group['new_name'], $emptyGroups)
            || $group['original_name'] === \App\Http\Controllers\ChannelGroupController::NONAMEGROUP)
                <button data-id="{{$group['id']}}"
                        data-name="{{$group['new_name']}}"
                        class="group-delete-btn btn btn-default"
                        type="button"><span class="glyphicon glyphicon-remove"></span></button>
            @endif
        </span>
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
                    {!! ($group['hidden'] !== 0) ? 'style="opacity: 0.4;"' : ''!!}>
                <thead>
                <tr>
                    <th class="narrow-col"></th>
                    {{--<th>Оригинальное название</th>--}}
                    <th>Название</th>
                    <th>Группа</th>
                    <th>Ссылка</th>
                    <th style="width: 10%">Скрыть</th>
                    <th style="width: 4%"></th>
                    <th style="width: 4%"></th>
                </tr>
                </thead>
                <tbody class="sortable sortable-channels">
                @foreach($channels as $channel)
                    @if($channel['group_id'] === $group['id'])
                        @include('admin.include.channel')
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>