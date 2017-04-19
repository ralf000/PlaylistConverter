<div class="sort-element">
    <input type="hidden" class="id-input" name="{{$group['id']}}[id]"
           value="{{$group['id']}}">
    <input type="hidden" name="{{$group['id']}}[original_name]"
           value="{{$group['original_name']}}">
    <input type="hidden" name="{{$group['id']}}[sort]" class="sort"
           value="{{$group['sort']}}">
    <input type="hidden" name="{{$group['id']}}[disabled]" class="disable-tag"
           value="{{ $group['hidden'] }}">

    <div class="form-group">
        <span style="color: gray;">Оригинальное название: {{$group['original_name']}}</span>
        <div class="input-group">
            <span class="input-group-btn">
                <span class="btn btn-default glyphicon glyphicon-move sorting-btn"></span>
            </span>
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
                            type="button">Скрыть</button>
                @else
                    <button data-id="{{$group['id']}}"
                            data-token="{{ csrf_token() }}"
                            class="change-visibility-btn element-show-btn btn btn-default"
                            type="button">Показать</button>
                @endif
                @if ($group['own'])
                    <button data-id="{{$group['id']}}"
                            data-element-name="{{$group['new_name']}}"
                            class="element-delete-btn btn btn-default"
                            type="button"><span class="glyphicon glyphicon-remove"></span></button>
                @endif
            </span>
        </div>
    </div>
</div>