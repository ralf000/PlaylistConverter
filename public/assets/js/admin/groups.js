var groups_delay = 600;

function updateGroupsForm(form, data) {
    var output = '';
    var hiddenInputs = [];
    var inputs = [];
    var fields = [];
    $.each(data, function (id, el) {
        hiddenInputs[id] = '<input type="hidden" class="id-input" name="' + el.id + '[id]" value="' + el.name + '">';
        hiddenInputs[id] = '<input type="hidden" name="' + el.id + '[original_name]" value="' + el.original_name + '">';
        inputs[id] =
            '<div class="form-group">\n\
                <span style="color: gray;">Оригинальное название: ' + el.original_name + '</span>\n\
                <div class="input-group">\n\
                    <input name="' + el.id + '[new_name]"\n\
                           type="text" class="form-control"\n\
                           id="' + el.id + '"\n\
                           placeholder="' + el.new_name + '"\n\
                           value="' + el.new_name + '">\n\
                    <span class="input-group-btn">\n\
                    <button data-id="' + el.id + '"\n\
                        data-element-name="' + el.new_name + '"\n\
                        class="element-delete-btn btn btn-default"\n\
                        type="button">\n\
                        <span class="glyphicon glyphicon-remove"></span>\n\
                    </button>\n\
                    </span>\n\
                </div>\n\
            </div>';
    });
    output += hiddenInputs.join("\n");
    output += inputs.join("\n");
    form.children('input[name=_token]').after(output);

    addDeleteElementHandler();
}

function sendDeleteAjax() {
    $.ajax({
        method: 'post',
        url: '/admin/ajax/update-groups-from-playlist',
        data: {
            "_token": $(this).data('token')
        }
    }).done(function (data) {
        if (data.error === 0) {
            alert(data.error);
        } else {
            var form = $('form#groups-form');
            var fields = form.children('.form-group').slice(0, -1).add('input.id-input');
            fields.fadeOut(groups_delay);
            setTimeout(function () {
                fields.remove();
                updateGroupsForm(form, data);
            }, groups_delay);
        }
    });
}

function sendChangeVisibilityAjax() {
    $.ajax({
        method: 'post',
        url: '/admin/ajax/change-group-visibility',
        data: {
            "_token": $(this).data('token'),
            id: $(this).data('id')
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
            sendDeleteAjax();
        }
    });

    //скрыть/показать группу в списке групп
    $('button.change-visibility-btn').on('click', function (e) {
        e.preventDefault();
        var btn = $(this);
        $.ajax({
            method: 'post',
            url: '/admin/ajax/change-group-visibility',
            data: {
                "_token": $(this).data('token'),
                id: $(this).data('id')
            }
        }).done(function (data) {
            if (data.error === 0) {
                alert(data.error);
            } else {
                console.log(data);
            }
        });
        if (btn.hasClass('element-hide-btn')) {
            btn
                .removeClass('element-hide-btn')
                .addClass('element-show-btn')
                .text('Показать')
                .closest('.form-group')
                .find('input[type=text]')
                .css('opacity', '0.4')
                .attr('disabled', 'disabled');
        } else {
            btn
                .removeClass('element-show-btn')
                .addClass('element-hide-btn')
                .text('Скрыть')
                .closest('.form-group')
                .find('input[type=text]')
                .removeAttr('style')
                .removeAttr('disabled');
        }
    });

    addSorting();
}

/**
 * Сортировка групп
 */
function addSorting() {
    //сортировка групп
    $("#sortable").sortable({
        revert: true
    });

    $("#draggable").draggable({
        connectToSortable: "#sortable",
        helper: "clone",
        revert: "invalid"
    });
    $("ul, li").disableSelection();
}

$(function () {
    addHandlersForGroups();
    addSorting();
});