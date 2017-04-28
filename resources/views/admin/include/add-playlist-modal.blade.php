<!-- Modal -->
<div class="modal fade" id="add-playlist" tabindex="-1" role="dialog" aria-labelledby="add-playlist-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="add-playlist-label">Добавить каналы из плейлиста</h4>
            </div>
            <form action="{{route('playlist-store')}}" method="post">
                {{ method_field('put') }}
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="playlist">Список каналов</label>
                        <textarea name="playlist" class="form-control" id="playlist" cols="30" rows="20"
                                  placeholder="Вставьте каналы из плейлиста"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="group">Группа</label>
                        <select name="original_group_id" id="group" class="form-control">
                            <option value="-1">распределить автоматически</option>
                            @foreach($groups as $groupModal)
                                <option value="{{$groupModal['id']}}">
                                    {{$groupModal['new_name']}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="help-block">Поле «список каналов» предназначено для массовой загрузки каналов из других плейлистов
                        списком.</p>
                    <p class="help-block">Загружаемые каналы должны иметь заголовок и url (группа не обязательна).
                        Тег #EXTM3U не обязателен</p>
                    <p class="help-block"><b>Примеры:</b></p>
                    <p class="help-block code-block">
                        <code>
                            #EXTINF:0,Название канала<br>http://ссылка-на-канал
                        </code>
                    </p>
                    <p class="help-block code-block">
                        <code>#EXTINF:0,Название канала<br>#EXTGRP:новости<br>http://ссылка-на-канал
                        </code>
                    </p>
                    <p class="help-block code-block">
                        <code>#EXTINF:-1 group-title="Группа канала",Название канала<br>http://ссылка-на-канал
                        </code>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary pull-right" id="add-playlist-btn">Добавить</button>
                </div>
            </form>
        </div>
    </div>
</div>