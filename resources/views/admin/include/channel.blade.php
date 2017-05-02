<tr class="sort-element">
    <input type="hidden" class="id-input" name="channel[{{$channel['id']}}][id]"
           value="{{$channel['id']}}">
    <input type="hidden" name="channel[{{$channel['id']}}][original_name]"
           value="{{$channel['original_name']}}"
           class="original-name">
    <input type="hidden" name="channel[{{$channel['id']}}][original_url]"
           value="{{$channel['original_url']}}"
           class="original-url">
    <input type="hidden" name="channel[{{$channel['id']}}][original_group_id]"
           value="{{$channel['original_group_id']}}"
           class="original-group-id">
    <input type="hidden" name="channel[{{$channel['id']}}][own]"
           value="{{ $channel['own'] }}">
    <input type="hidden" name="channel[{{$channel['id']}}][sort]" class="sort"
           value="{{$channel['sort']}}">
    <input type="hidden" name="channel[{{$channel['id']}}][disabled]" class="disable-tag"
           value="{{ $channel['hidden'] }}">
    <td><span class="btn btn-default glyphicon glyphicon-move sorting-btn" title="Сортировать"></span></td>
    <td {!! ($channel['hidden'] !== 0) ? 'style="opacity: 0.4;"' : ''!!}>
        <input name="channel[{{$channel['id']}}][new_name]"
               type="text" class="form-control new-name"
               id="{{$channel['id']}}"
               placeholder="{{$channel['new_name']}}"
               value="{{$channel['new_name']}}"
                {{($channel['hidden'] !== 0) ? 'readonly="readonly"' : ''}}>
    </td>
    <td {!! ($channel['hidden'] !== 0) ? 'style="opacity: 0.4;"' : ''!!}>
        <select name="channel[{{$channel['id']}}][group_id]"
                id="group"
                class="form-control group-id"
                {{($channel['hidden'] !== 0) ? 'readonly="readonly"' : ''}}>
            @foreach($groups as $groupForSelect)
                <option value="{{$groupForSelect['id']}}"
                        {{ ($channel['group_id'] === $groupForSelect['id']) ? 'selected' : '' }}>
                    {{$groupForSelect['new_name']}}
                </option>
            @endforeach
        </select>
    </td>
    <td {!! ($channel['hidden'] !== 0) ? 'style="opacity: 0.4;"' : ''!!} class="td-url">
        <input name="channel[{{$channel['id']}}][new_url]"
               type="url" class="form-control new-url"
               id="new_url"
               value="{{$channel['new_url']}}" {{($channel['hidden'] !== 0) ? 'readonly="readonly"' : ''}}>
    </td>

    <td>
        @if($channel['hidden'] === 0)
            <button data-id="{{$channel['id']}}"
                    data-token="{{ csrf_token() }}"
                    class="change-channel-visibility-btn channel-hide-btn btn btn-default"
                    type="button">Скрыть
            </button>
        @else
            <button data-id="{{$channel['id']}}"
                    data-token="{{ csrf_token() }}"
                    class="change-channel-visibility-btn channel-show-btn btn btn-default"
                    type="button">Показать
            </button>
        @endif
    </td>
    <td>
        <button data-id="{{$channel['id']}}"
                data-name="{{$channel['new_name']}}"
                class="channel-reset-btn btn btn-default"
                title="Сбросить изменения">
            <span class="glyphicon glyphicon-share-alt"></span>
        </button>
    </td>
    <td>
        @if ($channel['own'])
            <button data-id="{{$channel['id']}}"
                    data-name="{{$channel['new_name']}}"
                    data-token="{{ csrf_token() }}"
                    class="channel-delete-btn btn btn-default"
                    title="Удалить канал"
                    type="button"><span class="glyphicon glyphicon-remove"></span></button>
        @endif
    </td>

</tr>