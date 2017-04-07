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
 * Обновить список каналов из текущего плейлиста
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

$(document).ready(function () {

    $(".submenu > a").click(function (e) {
        e.preventDefault();
        var $li = $(this).parent("li");
        var $ul = $(this).next("ul");

        if ($li.hasClass("open")) {
            $ul.slideUp(350);
            $li.removeClass("open");
        } else {
            $(".nav > li > ul").slideUp(350);
            $(".nav > li").removeClass("open");
            $ul.slideDown(350);
            $li.addClass("open");
        }
    });

    /**
     * Подсвечивает активный пункт бокового меню
     */
    var menuItems = $('.sidebar ul.nav li');
    menuItems.removeClass('current');
    var path = location.href;
    var item = menuItems.find('a[href="' + path + '"]');
    if (item && item.length === 1) {
        item.parent('li').addClass('current');
    }

    addHandlersCustom();
});