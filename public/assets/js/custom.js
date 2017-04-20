//globals
var sortedGroupsList = $(".sortable-groups");
var sortedChannelsList = $(".sortable-channels");


function addHandlersCustom() {
    addUpdateFromPlaylistHandler();
    addResetPlaylistHandler();
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
 * Обновление списка каналов из текущего плейлиста
 */
function addResetPlaylistHandler() {
    $('button#reset-playlist').on('click', function (e) {
        e.preventDefault();
        var message = 'Вы действительно хотите удалить все текущие группы и каналы?\n При этом данные из текущего плейлиста будут загружены автоматически';
        if (confirm(message)) {
            $('form#reset-playlist-form').submit();
        }
    });
}

/**
 * Присвоение полям input.sort порядковых значений
 */
function sort() {
    var groups = sortedGroupsList.children('.sort-element');
    var channels;
    $.each(groups, function (groupIndex, group) {
        $(group).find('input.sort').val(groupIndex);
        channels = $(group).find('tbody.sortable tr.sort-element');
        $.each(channels, function (channelIndex, channel) {
            $(channel).find('input.sort').val(channelIndex);
        });
    });
}

/**
 * Ининциализация сортировки каналов
 */
function addSortingGroups() {
    //сортировка групп
    sortedGroupsList.sortable({
        revert: true,
        stop: function () {
            sort();
        }
    });
}

/**
 * Ининциализация сортировки каналов
 */
function addSortingChannels() {
    //сортировка групп
    sortedChannelsList.sortable({
        revert: true,
        stop: function () {
            sort();
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
    sort();
    addHandlersCustom();
});