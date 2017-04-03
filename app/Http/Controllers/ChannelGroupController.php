<?php

namespace App\Http\Controllers;

use App\ChannelGroup;
use App\Playlist;
use Illuminate\Http\Request;

class ChannelGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ChannelGroup $group
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ChannelGroup $group)
    {
        $title = 'Группы каналов';
        $groups = $group->all()->toArray();

        return view('admin.groups', compact('title', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $input = $request->except(['_token', '_method']);

        $validator = \Validator::make($input, [
            'original_name' => 'required|unique:channel_groups',
        ]);

        if ($validator->fails()) {
            return redirect()->route('groups')->withErrors($validator);
        }

        $group = new ChannelGroup();
        $group->fill($input);
        $group->new_name = $group->original_name;
        if ($group->save()) {
            return redirect()->route('groups')->with('status', 'Новая группа успешно добавлена');
        }

        throw new \Exception('При добавлении новой группы что-то пошло не так');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ChannelGroup $channelGroup
     * @return \Illuminate\Http\Response
     */
    public function show(ChannelGroup $channelGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->except(['_token']);

        foreach ($input as $groupData) {
            $validator = \Validator::make($groupData, [
                'new_name' => "required|unique:channel_groups,new_name,{$groupData['id']}"
            ]);
            if ($validator->fails()) {
                return redirect()->route('groups')->withErrors($validator);
            }
            $group = ChannelGroup::find($groupData['id']);
            $group->fill($groupData);
            $group->update();
        }
        return redirect()->route('groups')->with('status', 'Данные групп успешно обновлены');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        if (ChannelGroup::destroy((int)$request->id))
            return redirect()->route('groups')->with('status', 'Группа успешно удалена');
        throw new \Exception("Не удалось удалить группу с идентификатором {$request->id}");
    }

    public function updateGroupsFromPlaylist()
    {
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
            ]);
            $channelGroup->save();
        }
        return (new ChannelGroup())->all()->toArray();
    }
}
