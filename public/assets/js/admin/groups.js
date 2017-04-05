function sendChangeVisibilityAjax(el) {
    $.ajax({
        method: 'post',
        url: '/admin/ajax/change-group-visibility',
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

function addHandlersForGroups() {
    //Обновить список групп из текущего плейлиста
    $('button#update-groups').on('click', function (e) {
        e.preventDefault();
        var message = 'Вы действительно хотите обновить список групп из плейлиста, указанного в разделе "Настройки/Ссылка на плейлист"?';
        if (confirm(message)) {
            $('form#update-groups-form').submit();
        }
    });

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
                .closest('.form-group')
                .find('input[type=text]')
                .css('opacity', '0.4')
                .attr('disabled', 'disabled')
                .closest('.group-element')
                .find('input.disable-tag')
                .val(1);
        } else {
            btn
                .removeClass('element-show-btn')
                .addClass('element-hide-btn')
                .text('Скрыть')
                .closest('.form-group')
                .find('input[type=text]')
                .removeAttr('style')
                .removeAttr('disabled')
                .closest('.group-element')
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
    addHandlersForGroups();
    addSorting();
});