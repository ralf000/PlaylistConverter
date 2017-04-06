<?php

namespace App\Http\Controllers;

use App\DBChannel;
use App\ChannelGroup;
use App\Playlist;
use Illuminate\Http\Request;

class ChannelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param DBChannel $channel
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(DBChannel $channel)
    {
        $title = 'Каналы';
        return view('admin.channels', compact('title', 'channels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        $channelsFromPlaylist = $playlist->getChannelsFromPlaylist();
        $groupsFromDB = ChannelGroup::all('id','original_name')->toArray();
        $preparedGroupsFromDB = [];
        foreach ($groupsFromDB as $groupFromDB) {
            $preparedGroupsFromDB[$groupFromDB['id']] = mb_strtolower($groupFromDB['original_name']);
        }
        $addedChannels = DBChannel::all(['new_name'])->toArray();
        $preparedAddedChannels = [];
        foreach ($addedChannels as $addedChannel) {
            $preparedAddedChannels[] = mb_strtolower($addedChannel['new_name']);
        }
        foreach ($channelsFromPlaylist as $channelFromPlaylist => $group) {
            $group = mb_strtolower($group);
            if (in_array(mb_strtolower($channelFromPlaylist), $preparedAddedChannels)) continue;
            if (!in_array($group, $preparedGroupsFromDB)) continue;

            $channel = new DBChannel();
            $channel->fill([
                'original_name' => $channelFromPlaylist,
                'new_name' => $channelFromPlaylist,
                'sort' => ++$maxSortValue,
                'group_id' => array_search($group, $preparedGroupsFromDB)
            ]);
            $channel->save();
        }

        return session()->flash('status', 'Список каналов успешно обновлен из плейлиста');
    }

}
