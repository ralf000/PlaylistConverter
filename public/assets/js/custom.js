function addHandlersCustom() {
    addUpdateFromPlaylistHandler();
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
function initElementsPosition(sortedList) {
    var elements = sortedList.children('.sort-element');
    $.each(elements, function (id, element) {
        var index = $(element).index();
        $(element).find('input.sort').val(index);
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
    addHandlersCustom();
});