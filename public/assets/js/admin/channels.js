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
        var tdUrl = btn.closest('tr').find('td.td-url');
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
                .val(1);
            if (tdUrl.length !== 0){
                tdUrl.css('opacity', '0.4')
                    .children('input[type=url')
                    .attr('disabled', 'disabled');
            }
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
            if (tdUrl.length !== 0){
                tdUrl.removeAttr('style')
                    .children('input[type=url')
                    .removeAttr('disabled');
            }
        }
    });

    addSorting();
}

$(function () {
    initElementsPosition();
    addHandlersForChannels();
    addSorting();
});