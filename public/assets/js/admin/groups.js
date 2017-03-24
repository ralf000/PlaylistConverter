var groups_delay = 600;

function updateGroupsForm(form, data) {
    var output = '';
    var hiddenInputs = [];
    var inputs = [];
    var fields = [];
    $.each(data, function (id, el) {
        hiddenInputs[id] = '<input type="hidden" class="id-input" name="' + el.id + '" value="' + el.name + '">';
        inputs[id] =
            '<div class="form-group">\n\
                <div class="input-group">\n\
                    <input name="' + el.id + '[name]"\n\
                           type="text" class="form-control"\n\
                           id="' + el.id + '"\n\
                           placeholder="' + el.name + '"\n\
                           value="' + el.name + '">\n\
                    <span class="input-group-btn">\n\
                    <button data-id="' + el.id + '"\n\
                        data-element-name="' + el.name + '"\n\
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
    
    addDeleteElementsHandler();
}

$(function () {
    $('button#update-groups').on('click', function (e) {
        e.preventDefault();
        var message = 'Вы действительно хотите обновить список групп из плейлиста, указанного в разделе "Настройки/Ссылка на плейлист"?';
        if (confirm(message)) {
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
    })
});