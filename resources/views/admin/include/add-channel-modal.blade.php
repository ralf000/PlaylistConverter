<!-- Modal -->
<div class="modal fade" id="add-channel" tabindex="-1" role="dialog" aria-labelledby="add-channel-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="add-channel-label">Добавить канал</h4>
            </div>
            <form action="{{route('channels-store')}}" method="post">
                {{ method_field('put') }}
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="title">Название канала</label>
                        <input name="original_name" type="text" class="form-control" id="title"
                               placeholder="Введите название канала">
                    </div>
                    <div class="form-group">
                        <label for="url">Ссылка</label>
                        <input name="url" type="url" class="form-control" id="url"
                               placeholder="Введите ссылку на канал">
                    </div>
                    <div class="form-group">
                        <label for="group">Группа</label>
                        <select name="group_id" id="group" class="form-control">
                            @foreach($groups as $groupModal)
                                <option value="{{$groupModal['id']}}">
                                    {{$groupModal['new_name']}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary pull-right" id="add-channel-btn">Добавить</button>
                </div>
            </form>
        </div>
    </div>
</div>