<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">{{ $title }}</div>
    </div>

    @include('admin.include.errors&messages')

    <div class="panel-body">
        @if(!empty($data) && is_array($data))
            <form class="form-horizontal" role="form" action="{{ route('config-update') }}" method="post">
                {{ csrf_field() }}
                @foreach ($data as $key => $item)
                    <div class="form-group">
                        <label for="{{$key}}" class="col-sm-2 control-label">{{$item['label']}}</label>
                        <div class="col-sm-10">
                            <input name="{{$key}}" type="text" class="form-control" id="{{$key}}"
                                   placeholder="{{$item['label']}}"
                                   value="{{old($key) ?: $item['value']}}">
                        </div>
                    </div>
                @endforeach
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </form>
        @else
            <p>Отсутствуют данные для отображения</p>
        @endif
    </div>
</div>