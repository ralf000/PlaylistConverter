/**
 * Посылает ajax запрос для получения списка телеканалов для которых не найдена телепрограмма
 *
 * @param params string (?name=value&name=value)
 */
function getNotFoundChannels(params) {
    params = params || '';
    var targetBlock = $('#not-found-channels');
    targetBlock.empty();
    targetBlock.append('<img src="/img/loading.gif" class="center-block" alt="loading" />');
    $.ajax('/admin/ajax/get-not-found-channels' + params)
        .done(function (data) {
            targetBlock.empty();
            targetBlock.append('<small class="pull-right">Последнее обновление: <b>' + data['date'] + '</b></small>');
            targetBlock.append('<div>' + data['channels'] + '</div>');
        })
        .error(function () {
            var msg = 'Не удалось проверить телепрограмму. Проверьте доступность файла телепрограммы.';
            targetBlock.append(msg)
        });
}

function addAdminHandlers() {
    $('#refresh-not-found-channels').click(function () {
        getNotFoundChannels('?reset-cache=true');
    });
}

$(function () {
    getNotFoundChannels();
    addAdminHandlers()
});