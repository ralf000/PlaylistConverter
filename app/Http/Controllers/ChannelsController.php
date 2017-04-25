<?php

namespace App\Http\Controllers;

use App\DBChannel;
use App\ChannelGroup;
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
        $title = 'Плейлист';
        $channels = DBChannel::all()->sortBy('sort')->toArray();
        $groups = ChannelGroup::all()->sortBy('sort')->toArray();
        $emptyGroups = $this->getEmptyGroups();

        return view('admin.channels', compact('title', 'groups', 'channels', 'emptyGroups'));
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
            'original_name' => 'required',
            'original_url' => 'required|unique:channels|url',
            'original_group_id' => 'required|integer|exists:channel_groups,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('channels')->withErrors($validator);
        }

        $channel = new DBChannel();
        $channel->fill($input);
        $channel->new_name = $channel->original_name;
        $channel->new_url = $channel->original_url;
        $channel->group_id = $channel->original_group_id;
        $channel->sort = ++$maxSortValue;
        $channel->own = 1;
        if ($channel->save()) {
            return redirect()->route('channels')->with('status', 'Новый канал успешно добавлен');
        }

        throw new \Exception('При добавлении нового канала что-то пошло не так');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Request $request)
    {
        (new ChannelGroupController())->update($request);

        $input = $request->except(['_token'])['channel'];

        foreach ($input as $id => $channelData) {

            $validationRules = [
                'id' => 'required|integer',
                'sort' => 'required|integer',
                'disabled' => 'required|integer',
            ];
            if (!$channelData['disabled']) {
                $validationRules += [
                    'new_name' => "required",
                    'new_url' => "required|url|unique:channels,new_url,{$id}",
                    'group_id' => 'required|integer|exists:channel_groups,id',
                ];
            }
            $validator = \Validator::make($channelData, $validationRules);
            if ($validator->fails()) {
                return redirect()->route('channels')->withErrors($validator);
            }
            $channel = DBChannel::find($channelData['id']);

            if ($channel->original_name !== $channelData['original_name']
                || $channel->original_url !== $channelData['original_url']
                || (int)$channel->original_group_id !== (int)$channelData['original_group_id']
            ) {
                throw new \Exception('Переданы неверные данные для канала ' . $channel->new_name);
            }
            $channel->fill($channelData);
            $channel->update();
        }
        $this->checkNonameGroup();

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

            $channelGroupController = new ChannelGroupController();
            if ($channelGroupController->emptyNonameGroup())
                $channelGroupController->destroyNonameGroup();

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

    /**
     * проверяет группу для каналов без группы на пустоту и удаляет
     */
    private function checkNonameGroup()
    {
        $channelGroupController = new ChannelGroupController();
        if ($channelGroupController->emptyNonameGroup())
            $channelGroupController->destroyNonameGroup();
    }

    /**
     * Возвращает список пустых групп без каналов
     *
     * @return array
     */
    private function getEmptyGroups() : array
    {
        $emptyGroups = ChannelGroup::select('channel_groups.new_name')->whereNull('channels.group_id')->leftJoin('channels', 'channel_groups.id', '=', 'channels.group_id')->get();
        $output = [];
        foreach ($emptyGroups as $emptyGroup) {
            $output[] = $emptyGroup->new_name;
        }
        return $output;
    }

}
