function sendChangeVisibilityAjax(el) {
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

function addHandlersForChannels() {
    //скрыть/показать группу в списке групп
    $('button.change-visibility-btn').on('click', function (e) {
        e.preventDefault();
        var btn = $(this);
        sendChangeVisibilityAjax($(this));
        if (btn.hasClass('element-hide-btn')) {
            btn
                .removeClass('element-hide-btn')
                .addClass('element-show-btn')
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
                .val(1)
        } else {
            btn
                .removeClass('element-show-btn')
                .addClass('element-hide-btn')
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
        }
    });

    addSorting();
}

function initGroupsPosition() {
    var groups = $('.groups-list').children('.group-element');
    $.each(groups, function (id, group) {
        var index = $(group).index();
        $(group).children('input.sort').val(index);
    })
}

/**
 * Сортировка групп
 */
function addSorting() {
    //сортировка групп
    $("#sortable").sortable({
        revert: true,
        stop: function () {
            initGroupsPosition();
        }
    });
}

$(function () {
    addHandlersForChannels();
    addSorting();
});