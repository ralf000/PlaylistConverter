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
        //$groups = $this->filterGroups();
        $groups = ChannelGroup::all()->sortBy('sort')->toArray();
        $groupsWithOwnChannels = ChannelGroup::where('channels.own', 1)
            ->join('channels', 'channel_groups.id', '=', 'channels.group_id')
            ->get(['channel_groups.id'])
            ->toArray();

        return view('admin.channels', compact('title', 'groups', 'channels', 'groupsWithOwnChannels'));
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
     * @throws \Exception
     */
    public function store(Request $request)
    {
        //масимальное значение поля sort для сортировки новых добавляемых каналов
        $maxSortValue = DBChannel::all('sort')->max('sort');

        $input = $request->except(['_token', '_method']);

        $validator = \Validator::make($input, [
            'original_name' => 'required|unique:channels',
            'url' => 'required|unique:channels|url',
            'group_id' => 'required|integer|exists:channel_groups,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('channels')->withErrors($validator);
        }

        $channel = new DBChannel();
        $channel->fill($input);
        $channel->new_name = $channel->original_name;
        $channel->sort = ++$maxSortValue;
        $channel->own = 1;
        if ($channel->save()) {
            return redirect()->route('channels')->with('status', 'Новый канал успешно добавлен');
        }

        throw new \Exception('При добавлении нового канала что-то пошло не так');
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->except(['_token']);

        foreach ($input as $channelData) {

            $validationRules = [
                'id' => 'required|integer',
                'original_name' => "required",
                'sort' => 'required|integer',
                'disabled' => 'required|integer',
            ];
            if ($channelData['own']){
                $validationRules += [
                    'url' => "required|url|unique:channels,url,{$channelData['id']}"
                ];
            }
            if (!$channelData['disabled']) {
                $validationRules += [
                    'new_name' => "required|unique:channels,new_name,{$channelData['id']}",
                    'group_id' => 'required|integer|exists:channel_groups,id',
                ];
            }
            $validator = \Validator::make($channelData, $validationRules);
            if ($validator->fails()) {
                return redirect()->route('channels')->withErrors($validator);
            }
            $channel = DBChannel::find($channelData['id']);
            $channel->fill($channelData);
            $channel->update();
        }
        return redirect()->route('channels')->with('status', 'Изменения успешно сохранены');
    }

    /**
     * Remove the specified resource from storage.
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        $channel = DBChannel::find((int)$request->id);
        //если канал добавлен пользователем (own === 1) и передан верный id
        if ($channel && $channel->own) {
            DBChannel::destroy($channel->id);
            return redirect()->route('channels')->with('status', 'Канал успешно удален');
        }
        throw new \Exception("Не удалось удалить канал с идентификатором {$request->id}");
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

    /*private function filterGroups()
    {
        $groups = ChannelGroup::where('hidden', 0)->orderBy('sort')->get();
        $preparedGroups = [];
        foreach ($groups as $group) {
            if (count($group->channels) !== 0)
                $preparedGroups[] = $group->toArray();
        }
        return $preparedGroups;
    }*/

}
