function addHandlersCustom() {
    addDeleteElementHandler();
    // addChangeVisibilityElementHandler();
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

/*function addChangeVisibilityElementHandler() {
    var btn = $('button.change-visibility-btn');
    btn.unbind();
    btn.on('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('element-name');
        var form = $('form#change-visibility');
        form.find('input[name=id]').attr('value', id);
        var showHideWord = (btn.hasClass('element-hide-btn')) ? 'скрыть' : 'показать';
        if (confirm('Вы действительно хотите ' + showHideWord + ' элемент ' + '"' + name + '"?'))
            form.submit();
    });
}*/

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