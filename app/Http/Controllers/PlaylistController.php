<?php

namespace App\Http\Controllers;

use App\Channel;
use App\ChannelGroup;
use App\Config;
use App\DBChannel;
use App\Helpers\Log;
use App\Helpers\MbString;
use App\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        //масимальное значение поля sort для сортировки новых добавляемых каналов
        $maxSortValue = DBChannel::all('sort')->max('sort');

        $input = $request->except(['_token', '_method']);

        $rules = ['playlist' => 'required'];
        if ((int)$input['original_group_id'] !== -1)
            $rules += ['original_group_id' => 'required|integer|exists:channel_groups,id'];
        else
            $rules += ['original_group_id' => 'required'];

        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->route('channels')->withErrors($validator);
        }

        $tmpFile = $this->createAndWriteTmpFileForPlaylist($input['playlist']);
        $playlist = new Playlist();
        $playlist->setDescriptor($tmpFile);
        $channels = $playlist->getRawChannels();
        if (!$channels || !is_array($channels))
            throw new \Exception('При добавлении каналов из плейлиста что-то пошло не так');

        foreach ($channels as $channel) {
            /**
             * @var Channel $channel
             */
            if (!$channel->getTitle() || !$channel->getUrl())
                continue;

            if ((int)$input['original_group_id'] === -1) {
                $groupId = (new ChannelGroupController())->getGroupIdForInputChannel($channel);
            } else {
                $groupId = $input['original_group_id'];
            }

            $channelData = [
                'original_name' => $channel->getTitle(),
                'original_url' => $channel->getUrl(),
                'original_group_id' => $groupId
            ];
            (new ChannelsController())->saveChannel($channelData);
        }

        return redirect()->route('channels')->with('status', 'Новые каналы из плейлиста успешно добавлены');
    }

    /**
     * Проверяет добавлены ли каналы и группы из плейлиста, указанного в настройках
     */
    public static function syncWithPlaylist()
    {
        if (Config::get('builderMode')) {
            return redirect()->back()
                ->with('info', 'Для данной операции необходимо убрать галочку "создать плейлист в нуля" в настройках');
        }
        self::updateGroupsFromPlaylist();
        self::updateChannelsFromPlaylist();

        Log::log('Каналы и группы синхронизированы с текущим плейлистом');

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
                'original_name' => MbString::mb_trim($groupFromPlaylist),
                'new_name' => MbString::mb_trim($groupFromPlaylist),
                'sort' => ++$maxSortValue
            ]);
            $channelGroup->save();
        }
    }

    /**
     * Получает все каналы из плейлиста и сохраняет те, которые отсутствуют в бд
     * Меняет url каналов при необходимости
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
        $addedChannels = DBChannel::all(['original_name', 'original_url', 'new_url'])->toArray();
        //подготовка массива с заголовками каналов из бд
        $preparedAddedChannelsTitles = [];
        foreach ($addedChannels as $addedChannel) {
            $preparedAddedChannelsTitles[] = mb_strtolower($addedChannel['original_name']);
        }
        //подготовка массива с url каналов из бд
        $preparedAddedChannelsUrls = [];
        foreach ($addedChannels as $addedChannel) {
            $preparedAddedChannelsUrls[mb_strtolower($addedChannel['original_name'])] = [
                'original_url' => mb_strtolower($addedChannel['original_url']),
                'new_url' => mb_strtolower($addedChannel['new_url'])
            ];
        }
        foreach ($channelsFromPlaylist as $channelFromPlaylist) {

            if (!self::needUpdateChannelFromPlaylist(
                $channelFromPlaylist,
                $preparedGroupsFromDB,
                $preparedAddedChannelsTitles,
                $preparedAddedChannelsUrls
            )
            ) continue;

            $group = mb_strtolower($channelFromPlaylist->getGroup());
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

    /**
     * @param string $data
     * @return resource
     */
    private function createAndWriteTmpFileForPlaylist(string $data)
    {
        $tmpFile = tmpfile();
        fwrite($tmpFile, $data);
        fseek($tmpFile, 0);
        return $tmpFile;
    }

    /**
     * Проверяет нужно ли добавить текущий канал из плейлиста в бд
     * Также Обновляет url канала в бд при необходимости
     *
     * @param Channel $channelFromPlaylist
     * @param array $preparedGroupsFromDB
     * @param array $preparedAddedChannelsTitles
     * @param array $preparedAddedChannelsUrls
     * @return bool
     */
    private static function needUpdateChannelFromPlaylist(
        Channel $channelFromPlaylist,
        array $preparedGroupsFromDB,
        array $preparedAddedChannelsTitles,
        array $preparedAddedChannelsUrls
    ) : bool
    {
        $title = mb_strtolower($channelFromPlaylist->getTitle());
        $url = mb_strtolower($channelFromPlaylist->getUrl());
        $group = mb_strtolower($channelFromPlaylist->getGroup());

        if (!in_array($group, $preparedGroupsFromDB)) return false;

        if (!in_array($title, $preparedAddedChannelsTitles)) return true;

        if ($url !== $preparedAddedChannelsUrls[$title]['original_url']) {
            $channelForUpdate = DBChannel::where('original_name', $title)->first();
            if ($channelForUpdate) {
                $channelData = ['original_url' => $url];
                if ($channelForUpdate->original_url === $channelForUpdate->new_url) {
                    $channelData += ['new_url' => $url];
                }
                $channelForUpdate->fill($channelData);
                $channelForUpdate->update();
            }
        }
        return false;
    }
}
