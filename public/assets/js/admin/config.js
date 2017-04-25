function initConfig() {
    if ($('#builderMode').is(':checked'))
        $('#inputPlaylist').attr('disabled', 'disabled');
    else
        $('#inputPlaylist').removeAttr('disabled');

    var playlistPathBlock = $('#output-playlist-path');
    var outputPlaylistName = $('input#outputPlaylistName');
    if (outputPlaylistName.val().length !== 0) {
        var playlistPathField = $('#output-playlist-path-field');
        playlistPathField.empty();
        var playlistPath = location.origin + '/' + outputPlaylistName.val();
        playlistPathField.append(playlistPath);
        playlistPathBlock.fadeIn();
    }else{
        playlistPathBlock.fadeOut();
    }
    var tvProgramPathBlock = $('#output-tvprogram-path');
    var outputTVProgramName = $('input#outputTVProgramName');
    if (outputTVProgramName.val().length !== 0) {
        var tvProgramPathField = $('#output-tvprogram-path-field');
        tvProgramPathField.empty();
        var tvProgramPath = location.origin + '/' + outputTVProgramName.val() + '.gz';
        tvProgramPathField.append(tvProgramPath);
        tvProgramPathBlock.fadeIn();
    } else {
        tvProgramPathBlock.fadeOut();
    }
}

function addConfigHandlers() {
    $('#builderMode').click(function () {
        initConfig();
    });
    $('input#outputPlaylistName').keyup(function () {
        initConfig();
    });
    $('input#outputTVProgramName').keyup(function () {
        initConfig();
    });
}

$(function () {
    initConfig();
    addConfigHandlers();
});