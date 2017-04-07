function addHandlersCustom() {
    addDeleteElementHandler();
    addUpdateFromPlaylistHandler();
}

/**
 * Обработтчик для удаления элементов
 */
function addDeleteElementHandler() {
    var btn = $('button.element-delete-btn');
    btn.unbind();
    btn.on('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('element-name');
        var form = $('form#element-delete');
        form.find('input[name=id]').attr('value', id);
        if (confirm('Вы действительно хотите удалить элемент ' + '"' + name + '"?'))
            form.submit();
    });
}

/**
 * Обновление списка каналов из текущего плейлиста
 */
function addUpdateFromPlaylistHandler() {
    $('button#update-from-playlist').on('click', function (e) {
        e.preventDefault();
        var message = 'Вы действительно хотите обновить список групп и каналов из плейлиста, указанного в разделе "Настройки/Ссылка на плейлист"?';
        if (confirm(message)) {
            $('form#update-from-playlist-form').submit();
        }
    });
}

/**
 * Присвоение полям input.sort порядковых значений
 */
function initElementsPosition() {
    var elements = $('.sortable').children('.sort-element');
    $.each(elements, function (id, element) {
        var index = $(element).index();
        $(element).find('input.sort').val(index);
    });
}

/**
 * Ининциализация сортировки
 */
function addSorting() {
    //сортировка групп
    $(".sortable").sortable({
        revert: true,
        stop: function () {
            initElementsPosition();
        }
    });
}

/**
 * Подсвечивает активный пункт бокового меню
 */
function initSidebar() {
    var menuItems = $('.sidebar ul.nav li');
    menuItems.removeClass('current');
    var path = location.href;
    var item = menuItems.find('a[href="' + path + '"]');
    if (item && item.length === 1) {
        item.parent('li').addClass('current');
    }
}

$(function () {
    initSidebar();
    initElementsPosition();
    addSorting();
    addHandlersCustom();
});