<div class="row">
    <div class="col-md-4">
        <div class="content-box-header">
            <div class="panel-title">Ссылка на обработанный плейлист</div>
        </div>
        <div class="content-box-large box-with-header" id="output-playlist-path-field">
            <h5>{{ $outputPlaylistPath }}</h5>
        </div>
        <div class="content-box-header">
            <div class="panel-title">Ссылка на обработанную телепрограмму</div>
        </div>
        <div class="content-box-large box-with-header" id="output-tvprogram-path-field">
            <h5>{{ $outputTVProgramPath }}</h5>
        </div>

        <div class="content-box-header">
            <div class="panel-title">Лента событий</div>
        </div>
        <div class="content-box-large box-with-header" id="logs">

        </div>

    </div>

    <div class="col-md-8">
        <div class="content-box-header">
            <div class="panel-title">Данные о текущей телепрограмме</div>
            <div class="panel-options">
                <button class="btn btn-xs btn-default" id="refresh-not-found-channels"><i class="glyphicon glyphicon-refresh"></i> Обновить</button>
            </div>
        </div>
        <div class="content-box-large box-with-header" id="not-found-channels">

        </div>
    </div>
</div>

<script src="{{ asset('/assets/js/admin/index.js') }}"></script>