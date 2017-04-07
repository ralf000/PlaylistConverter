<tr>
    <input type="hidden" class="id-input" name="{{$channel['id']}}[id]"
           value="{{$channel['id']}}">
    <input type="hidden" name="{{$channel['id']}}[original_name]"
           value="{{$channel['original_name']}}">
    <input type="hidden" name="{{$channel['id']}}[sort]" class="sort"
           value="{{$channel['sort']}}">
    <input type="hidden" name="{{$channel['id']}}[disabled]" class="disable-tag"
           value="{{ $channel['hidden'] }}">
    <td><span class="btn btn-default glyphicon glyphicon-move sorting-btn"></span></td>
    <td {!! ($channel['hidden'] !== 0) ? 'style="opacity: 0.4;"' : ''!!}>{{$channel['original_name']}}</td>
    <td {!! ($channel['hidden'] !== 0) ? 'style="opacity: 0.4;"' : ''!!}>
        <input name="{{$channel['id']}}[new_name]"
               type="text" class="form-control"
               id="{{$channel['id']}}"
               placeholder="{{$channel['new_name']}}"
               value="{{$channel['new_name']}}"
                {{($channel['hidden'] !== 0) ? 'disabled="disabled"' : ''}}>
    </td>
    <td {!! ($channel['hidden'] !== 0) ? 'style="opacity: 0.4;"' : ''!!}>
        <select name="{{$channel['id']}}[group_id]"
                id="group"
                class="form-control"
                {{($channel['hidden'] !== 0) ? 'disabled="disabled"' : ''}}>
            @foreach($allGroups as $group)
                <option value="{{$group['id']}}"
                        {{ ($channel['group_id'] === $group['id']) ? 'selected' : '' }}>
                    {{$group['new_name']}}
                </option>
            @endforeach
        </select>
    </td>
    <td>
        @if($channel['hidden'] === 0)
            <button data-id="{{$channel['id']}}"
                    data-token="{{ csrf_token() }}"
                    class="change-visibility-btn element-hide-btn btn btn-default"
                    type="button">Скрыть
            </button>
        @else
            <button data-id="{{$channel['id']}}"
                    data-token="{{ csrf_token() }}"
                    class="change-visibility-btn element-show-btn btn btn-default"
                    type="button">Показать
            </button>
        @endif
    </td>
    <td>
        @if ($channel['own'])
            <button data-id="{{$channel['id']}}"
                    data-element-name="{{$channel['new_name']}}"
                    class="element-delete-btn btn btn-default"
                    type="button"><span class="glyphicon glyphicon-remove"></span></button>
        @endif
    </td>

</tr>