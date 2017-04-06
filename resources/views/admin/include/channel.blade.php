<input type="hidden" class="id-input" name="{{$channel['id']}}[id]"
       value="{{$channel['id']}}">
<input type="hidden" name="{{$channel['id']}}[original_name]"
       value="{{$channel['original_name']}}">
<input type="hidden" name="{{$channel['id']}}[sort]" class="sort"
       value="{{$channel['sort']}}">
<input type="hidden" name="{{$channel['id']}}[disabled]" class="disable-tag"
       value="{{ $channel['hidden'] }}">
<td><span class="btn btn-default glyphicon glyphicon-move sorting-btn"></span></td>
<td>{{$channel['sort']}}</td>
<td>{{$channel['original_name']}}</td>
<td>
    @if($channel['hidden'] === 0)
        <input name="{{$channel['id']}}[new_name]"
               type="text" class="form-control"
               id="{{$channel['id']}}"
               placeholder="{{$channel['new_name']}}"
               value="{{$channel['new_name']}}">
    @else
        <input name="{{$channel['id']}}[new_name]"
               type="text" class="form-control"
               id="{{$channel['id']}}"
               placeholder="{{$channel['new_name']}}"
               style="opacity: 0.4"
               value="{{$channel['new_name']}}" disabled>
    @endif
</td>
<td>
    <select name="group" id="group" class="form-control">
        @foreach($groups as $group)
            {{ $selected = ($channel['group_id'] === $group['id']) ? 'selected' : '' }}
            <option value="{{$group['id']}}" {{$selected}}>{{$group['new_name']}}</option>
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
