function sendChangeChannelVisibilityAjax(el) {
    $.ajax({
        method: 'post',
        url: '/admin/ajax/change-channel-visibility',
        data: {
            "_token": el.data('token'),
            id: el.data('id')
        }
    }).done(function (data) {
        if (data.error === 0) {
            alert(data.error);
        } else {
            //если успешно - ничего не делать
        }
    });
}

function sendDeleteChannelAjax(el) {
    $.ajax({
        method: 'delete',
        url: '/admin/ajax/channel-delete',
        data: {
            "_token": el.data('token'),
            id: el.data('id')
        }
    }).done(function (data) {
        if (data.error === 0) {
            alert(data.error);
        } else {
        }
    });
}

/**
 * Обработтчик для удаления каналов
 */
function addDeleteChannelHandler() {
    var btn = $('button.channel-delete-btn');
    btn.on('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        // var form = $('form#channel-delete');
        //form.find('input[name=id]').attr('value', id);
        if (confirm('Вы действительно хотите удалить канал ' + '"' + name + '"?')) {
            sendDeleteChannelAjax($(this));
            $(this).closest('tr').fadeOut();
        }
    });
}

function addResetChannelHandler() {
    var btn = $('button.channel-reset-btn');
    btn.on('click', function (e) {
        e.preventDefault();
        var channel = $(this).closest('tr');
        var originalName = channel.children('input.original-name').val();
        var originalUrl = channel.children('input.original-url').val();
        var originalGroupId = channel.children('input.original-group-id').val();
        channel.find('input.new-name').val(originalName);
        channel.find('input.new-url').val(originalUrl);
        var groups = channel.find('select.group-id option');
        $.each(groups, function (id, group) {
            group = $(group);
            if (group.attr('selected'))
                group.removeAttr('selected');
            if (group.attr('value') == originalGroupId) {
                group.prop('selected', true);
            }
        });
    });
}

/**
 * скрыть/показать канал в списке каналов
 */
function addChangeVisibilityChannelsHandler() {
    $('.change-channel-visibility-btn').on('click', function (e) {
        e.preventDefault();
        var btn = $(this);
        var tdUrl = btn.closest('tr').find('td.td-url');

        sendChangeChannelVisibilityAjax($(this));

        if (btn.hasClass('channel-hide-btn')) {
            btn
                .removeClass('channel-hide-btn')
                .addClass('channel-show-btn')
                .text('Показать')
                .closest('tr')
                .find('td').slice(1, 4)
                .css('opacity', '0.4')
                .closest('tr')
                .find('td input[type=text]')
                .attr('disabled', 'disabled')
                .closest('tr')
                .find('td select#group')
                .attr('disabled', 'disabled')
                .closest('tr')
                .find('input.disable-tag')
                .val(1);
            if (tdUrl.length !== 0) {
                tdUrl.css('opacity', '0.4')
                    .children('input[type=url')
                    .attr('disabled', 'disabled');
            }
        } else {
            btn
                .removeClass('channel-show-btn')
                .addClass('channel-hide-btn')
                .text('Скрыть')
                .closest('tr')
                .find('td').slice(1, 4)
                .removeAttr('style')
                .closest('tr')
                .find('input[type=text]')
                .removeAttr('disabled')
                .closest('tr')
                .find('td select#group')
                .removeAttr('disabled')
                .closest('table')
                .find('input.disable-tag')
                .val(0);
            if (tdUrl.length !== 0) {
                tdUrl.removeAttr('style')
                    .children('input[type=url')
                    .removeAttr('disabled');
            }
        }
    });
}

function addHandlersForChannels() {
    addDeleteChannelHandler();
    addResetChannelHandler();
    addSortingChannels();
    addChangeVisibilityChannelsHandler();
}

$(function () {
    addHandlersForChannels();
});