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
            targetBlock.empty();
            var msg = 'Не удалось проверить телепрограмму. Проверьте доступность файла телепрограммы.';
            targetBlock.append(msg);
        });
}

function getLogs() {
    var targetBlock = $('#logs');
    targetBlock.empty();
    targetBlock.append('<img src="/img/loading.gif" class="center-block" alt="loading" />');
    var url = '/admin/ajax/get-logs';
    $.get(url)
        .done(function (data) {
            targetBlock.empty();

            if (data.length === 0)
                return targetBlock.append('Записей пока нет');

            var logs = [];
            $.each(data, function (id, log) {
                logs[id] = '<tr><td>' + log.date + '</td><td>' + escapeHtml(log.message) + '</td></tr>';
            });
            var table = '<table class="table"><tr><th style="width: 100px;">Дата</th><th>Сообщение</th></tr>'+logs.join('\n')+'</table>';
            targetBlock.append(table);
        })
        .error(function () {
            targetBlock.empty();
            var msg = 'Не удалось загрузить ленту событий';
            targetBlock.append(msg)
        });
}

function addAdminHandlers() {
    $('#refresh-not-found-channels').click(function () {
        getNotFoundChannels('?reset-cache=true');
    });
    $('#update-logs').click(function () {
        getLogs();
    });
}

$(function () {
    getNotFoundChannels();
    getLogs();
    addAdminHandlers()
});