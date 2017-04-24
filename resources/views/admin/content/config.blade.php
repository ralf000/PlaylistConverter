<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">{{ $title }}</div>
    </div>

    @include('admin.include.errors&messages')

    <div class="panel-body">
            <form class="form-horizontal" role="form" action="{{ route('config-update') }}" method="post">
                {{ csrf_field() }}
                <div class="checkbox">
                    <label>
                        <span class="col-sm-10">
                            <input type="checkbox" name="builderMode" id="builderMode"
                                    {{ $config['builderMode'] ? 'checked' : '' }}>
                            У меня нет самообновляемого плейлиста для редактирования.
                            Я хочу создать плейлист в нуля.
                        </span>
                    </label>
                </div><br>
                <div class="form-group">
                    <label for="inputPlaylist" class="col-sm-2 control-label">Ссылка на плейлист</label>
                    <div class="col-sm-10">
                        <input name="inputPlaylist" type="text" class="form-control" id="inputPlaylist"
                               placeholder="Введите ссылку на плейлист"
                               value="{{(old('inputPlaylist')) ?: $config['inputPlaylist']}}"
                                {{ ($config['builderMode']) ? 'disabled="disabled"' : ''}}>
                    </div>
                </div>
                <div class="form-group">
                    <label for="outputPlaylistName" class="col-sm-2 control-label">Имя готового плейлиста</label>
                    <div class="col-sm-10">
                        <input name="outputPlaylistName" type="text" class="form-control" id="outputPlaylistName"
                               placeholder="Введите желаемое имя"
                               value="{{(old('outputPlaylistName')) ?: $config['outputPlaylistName']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputTVProgram" class="col-sm-2 control-label">Ссылка на телепрограмму</label>
                    <div class="col-sm-10">
                        <input name="inputTVProgram" type="text" class="form-control" id="inputTVProgram"
                               placeholder="Введите ссылку на телепрограмму"
                               value="{{(old('inputTVProgram')) ?: $config['inputTVProgram']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputReserveTVProgram" class="col-sm-2 control-label">Ссылка на запасную
                        телепрограмму</label>
                    <div class="col-sm-10">
                        <input name="inputReserveTVProgram" type="text" class="form-control" id="inputReserveTVProgram"
                               placeholder="Введите ссылку на запасную телепрограмму"
                               value="{{(old('inputReserveTVProgram')) ?: $config['inputReserveTVProgram']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="outputTVProgramName" class="col-sm-2 control-label">Имя готовой телепрограммы</label>
                    <div class="col-sm-10">
                        <input name="outputTVProgramName" type="text" class="form-control" id="outputTVProgramName"
                               placeholder="Введите имя готовой телепрограммы"
                               value="{{(old('outputTVProgramName')) ?: $config['outputTVProgramName']}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </form>
    </div>
</div>
<script src="{{ asset('/assets/js/admin/config.js') }}"></script>