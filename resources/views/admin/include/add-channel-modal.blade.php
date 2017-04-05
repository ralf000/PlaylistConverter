<!-- Modal -->
<div class="modal fade" id="add-group" tabindex="-1" role="dialog" aria-labelledby="add-group-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="add-group-label">Добавить группу</h4>
            </div>
            <form action="{{route('groups-store')}}" method="post">
                {{ method_field('put') }}
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input name="original_name" type="text" class="form-control" id="title"
                           placeholder="Введите название группы">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary pull-right" id="add-group-btn">Добавить</button>
                </div>
            </form>
        </div>
    </div>
</div>