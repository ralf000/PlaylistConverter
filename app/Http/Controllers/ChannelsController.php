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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $title = 'Каналы';
        $channels = DBChannel::all()->sortBy('sort')->toArray();
        $groups = $this->filterGroups();

        return view('admin.channels', compact('title', 'groups', 'channels'));
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
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function changeChannelVisibility(Request $request)
    {
        $id = $request->id;
        if (!$id) throw new \Exception('Не указан id канала');
        $channel = DBChannel::find((int)$id);
        $channel->hidden = ($channel->hidden === 0) ? 1 : 0;
        return $channel->save();
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

    private function filterGroups()
    {
        $groups = ChannelGroup::where('hidden', 0)->orderBy('sort')->get();
        $preparedGroups = [];
        foreach ($groups as $group) {
            if (count($group->channels) !== 0)
                $preparedGroups[] = $group->toArray();
        }
        return $preparedGroups;
    }

}
