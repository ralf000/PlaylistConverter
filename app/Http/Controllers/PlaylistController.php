<?php

namespace App\Http\Controllers;

use App\Channel;
use App\ChannelGroup;
use App\Config;
use App\DBChannel;
use App\Helpers\Log;
use App\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{

    /**
     * Проверяет добавлены ли каналы и группы из плейлиста, указанного в настройках
     */
    public static function syncWithPlaylist()
    {
        if (Config::get('builderMode')) {
            return redirect()->back()
                ->with('info', 'Для данной операции необходимо убрать галочку "создать плейлист в нуля" в настроках');
        }
        self::updateGroupsFromPlaylist();
        self::updateChannelsFromPlaylist();

        Log::log('Каналы и группы синхронизированы с текущим плейлистом (' . Config::get('inputPlaylist') . ')');

        return redirect()->back()
            ->with('status', 'Список групп и каналов успешно обновлен из плейлиста');
    }

    /**
     * Получает все группы из плейлиста и сохраняет те, которые отсутствуют в бд
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function updateGroupsFromPlaylist()
    {
        //масимальное значение поля sort для сортировки новых добавляемых групп
        $maxSortValue = ChannelGroup::all('sort')->max('sort');

        $playlist = new Playlist();
        $groupsFromPlaylist = $playlist->getGroupsFromPlaylist();
        $addedGroups = ChannelGroup::all(['new_name'])->toArray();
        $preparedAddedGroups = [];
        foreach ($addedGroups as $addedGroup) {
            $preparedAddedGroups[] = mb_strtolower($addedGroup['new_name']);
        }
        foreach ($groupsFromPlaylist as $groupFromPlaylist) {
            if (in_array(mb_strtolower($groupFromPlaylist), $preparedAddedGroups)) continue;

            $channelGroup = new ChannelGroup();
            $channelGroup->fill([
                'original_name' => $groupFromPlaylist,
                'new_name' => $groupFromPlaylist,
                'sort' => ++$maxSortValue
            ]);
            $channelGroup->save();
        }
    }

    /**
     * Получает все каналы из плейлиста и сохраняет те, которые отсутствуют в бд
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function updateChannelsFromPlaylist()
    {
        //масимальное значение поля sort для сортировки новых добавляемых каналов
        $maxSortValue = DBChannel::all('sort')->max('sort');

        $playlist = new Playlist();
        $channelsFromPlaylist = $playlist->getRawChannels();
        $groupsFromDB = ChannelGroup::all('id', 'original_name')->toArray();
        $preparedGroupsFromDB = [];
        foreach ($groupsFromDB as $groupFromDB) {
            $preparedGroupsFromDB[$groupFromDB['id']] = mb_strtolower($groupFromDB['original_name']);
        }
        $addedChannels = DBChannel::all(['new_name'])->toArray();
        $preparedAddedChannels = [];
        foreach ($addedChannels as $addedChannel) {
            $preparedAddedChannels[] = mb_strtolower($addedChannel['new_name']);
        }
        foreach ($channelsFromPlaylist as $channelFromPlaylist) {
            /**
             * @var Channel $channelFromPlaylist
             */
            $group = mb_strtolower($channelFromPlaylist->getGroup());
            $title = mb_strtolower($channelFromPlaylist->getTitle());
            if (in_array($title, $preparedAddedChannels)) continue;
            if (!in_array($group, $preparedGroupsFromDB)) continue;

            $channel = new DBChannel();
            $channel->fill([
                'original_name' => $channelFromPlaylist->getTitle(),
                'new_name' => $channelFromPlaylist->getTitle(),
                'original_url' => $channelFromPlaylist->getUrl(),
                'new_url' => $channelFromPlaylist->getUrl(),
                'sort' => ++$maxSortValue,
                'original_group_id' => array_search($group, $preparedGroupsFromDB),
                'group_id' => array_search($group, $preparedGroupsFromDB)
            ]);
            $channel->save();
        }
    }

    /**
     * Очищает таблицу channel_groups
     */
    public static function resetPlaylist()
    {
        $groupsIdWithOwnChannels = [];
        $channels = DBChannel::all();
        foreach ($channels as $channel) {
            if ($channel->own !== 1)
                DBChannel::destroy($channel->id);
            else
                $groupsIdWithOwnChannels[] = $channel->group_id;
        }
        $groupsIdWithOwnChannels = array_unique($groupsIdWithOwnChannels);
        $groups = ChannelGroup::all();
        foreach ($groups as $group) {
            if (!in_array($group->id, $groupsIdWithOwnChannels))
                ChannelGroup::destroy($group->id);
        }
        Log::log('Все каналы и группы, кроме пользовательских, удалены');

        return redirect()->route('channels')->with('status', 'Плейлист успешно очищен');
    }

}
