function initConfig() {
    if ($('#builderMode').is(':checked'))
        $('#inputPlaylist').attr('disabled', 'disabled');
    else
        $('#inputPlaylist').removeAttr('disabled');
}

$(function () {
    initConfig();
    $('#builderMode').click(function () {
        initConfig();
    });
});