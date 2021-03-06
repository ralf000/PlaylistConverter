
function sendChangeGroupVisibilityAjax(el) {
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

/**
 * Обработтчик для удаления элементов
 */
function addDeleteGroupHandler() {
    var btn = $('button.group-delete-btn');
    btn.on('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        var form = $('form#group-delete');
        form.find('input[name=id]').attr('value', id);
        if (confirm('Вы действительно хотите удалить группу ' + '"' + name + '"?\n Все каналы из группы будут перемещены во временную группу "Без группы"'))
            form.submit();
    });
}

function addResetGroupHandler() {
    var btn = $('button.group-reset-btn');
    btn.on('click', function (e) {
        e.preventDefault();
        var group = $(this).closest('.group-block');
        var originalName = group.children('input.original-name').val();
        group.find('.form-group input.new-name').val(originalName);
    });
}

/**
 * скрыть/показать группу в списке групп
 */
function addChangeVisibilityGroupHandler() {
    $('.change-group-visibility-btn').on('click', function (e) {
        e.preventDefault();
        console.log(1);
        var btn = $(this);
        sendChangeGroupVisibilityAjax($(this));
        if (btn.hasClass('group-hide-btn')) {
            btn
                .removeClass('group-hide-btn')
                .addClass('group-show-btn')
                .text('Показать')
                .closest('.form-group')
                .find('input[type=text]')
                .css('opacity', '0.4')
                .attr('disabled', 'disabled')
                .closest('.form-group')
                .next('.row')
                .find('table')
                .css('opacity', '0.4')
                .closest('.sort-element')
                .find('input.disable-tag')
                .val(1);
        } else {
            btn
                .removeClass('group-show-btn')
                .addClass('group-hide-btn')
                .text('Скрыть')
                .closest('.form-group')
                .find('input[type=text]')
                .removeAttr('style')
                .removeAttr('disabled')
                .closest('.form-group')
                .next('.row')
                .find('table')
                .removeAttr('style')
                .closest('.sort-element')
                .find('input.disable-tag')
                .val(0);
        }
    });
}

function addHandlersForGroups() {
    addDeleteGroupHandler();
    addResetGroupHandler();
    addSortingGroups();
    addChangeVisibilityGroupHandler();
}

$(function () {
    addHandlersForGroups();
});