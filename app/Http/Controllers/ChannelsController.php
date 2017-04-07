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
        $allGroups = ChannelGroup::all('id', 'new_name', 'sort')->sortBy('sort')->toArray();

        return view('admin.channels', compact('title', 'groups', 'channels', 'allGroups'));
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->except(['_token']);

        foreach ($input as $channelData) {

            $validationRules = [
                'id' => 'required|integer',
                'original_name' => "required|string",
                'sort' => 'required|integer',
                'disabled' => 'required|integer',
            ];
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
