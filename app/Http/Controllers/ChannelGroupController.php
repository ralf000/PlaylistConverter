<?php

namespace App\Http\Controllers;

use App\ChannelGroup;
use App\DBChannel;
use Illuminate\Http\Request;

class ChannelGroupController extends Controller
{
    /**
     * @const имя группы для каналов, не имеющих группы
     */
    const NONAMEGROUP = 'Без группы';

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        //масимальное значение поля sort для сортировки новых добавляемых групп
        $maxSortValue = ChannelGroup::all('sort')->max('sort');

        $input = $request->except(['_token', '_method']);

        $validator = \Validator::make($input, [
            'original_name' => 'required|unique:channel_groups',
        ]);

        if ($validator->fails()) {
            return redirect()->route('channels')->withErrors($validator);
        }

        $group = new ChannelGroup();
        $group->fill($input);
        $group->new_name = $group->original_name;
        $group->sort = ++$maxSortValue;
        $group->own = 1;
        if ($group->save()) {
            return redirect()->route('channels')->with('status', 'Новая группа успешно добавлена');
        }

        throw new \Exception('При добавлении новой группы что-то пошло не так');
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param Request $request
     * @return $this
     */
    public function update(Request $request)
    {
        $input = $request->except(['_token'])['group'];

        foreach ($input as $groupData) {

            $validationRules = [
                'id' => 'required|integer',
                'original_name' => "required",
                'sort' => 'required|integer',
                'disabled' => 'required|integer',
            ];

            if (!$groupData['disabled']) {
                $validationRules += [
                    'new_name' => "required|unique:channel_groups,new_name,{$groupData['id']}",
                ];
            }
            $validator = \Validator::make($groupData, $validationRules);
            if ($validator->fails()) {
                return redirect()->route('channels')->withErrors($validator);
            }
            $group = ChannelGroup::find($groupData['id']);
            $group->fill($groupData);
            $group->update();
        }
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
        $group = ChannelGroup::find((int)$request->id);
        if ($group) {
            $this->changeGroupForDeleteChannels($group->id);

            if ($this->emptyNonameGroup())
                $this->destroyNonameGroup();

            ChannelGroup::destroy($group->id);
            return redirect()->route('channels')->with('status', 'Группа успешно удалена');
        }
        throw new \Exception("Не удалось удалить группу с идентификатором {$request->id}");
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function changeGroupVisibility(Request $request)
    {
        $id = $request->id;
        if (!$id) throw new \Exception('Не указан id группы');
        $group = ChannelGroup::find((int)$id);
        $group->hidden = ($group->hidden === 0) ? 1 : 0;
        return $group->save();
    }

    /**
     * Меняет группу на группу для каналов без группы у каналов из удаляемой группы
     *
     * @param $deletedGroupId
     * @throws \Exception
     */
    private function changeGroupForDeleteChannels($deletedGroupId)
    {
        $channelsFromGroup = DBChannel::all()->where('group_id', $deletedGroupId);
        if (!count($channelsFromGroup))
            return;

        $nonameGroupId = $this->addNonameGroup();
        foreach ($channelsFromGroup as $channelFromGroup) {
            $channelFromGroup->group_id = $nonameGroupId;
            $channelFromGroup->save();
        }
    }

    /**
     * Проверяет группу для каналов для групп на пустоту
     *
     * @return bool
     */
    public function emptyNonameGroup() : bool
    {
        $nonameGroupId = $this->getNonameGroupId();
        $nonameGroupChannels = DBChannel::all()->where('group_id', $nonameGroupId);
        return count($nonameGroupChannels) === 0;
    }

    /**
     * Проверяет наличие группы для каналов без группы и каналов в ней
     *
     * @return bool
     */
    private function hasNonameGroup() : bool
    {
        $groupId = $this->getNonameGroupId();
        if ($groupId === false) return false;

        $channels = DBChannel::where('group_id', $groupId);
        if (count($channels)) return false;

        return true;
    }

    /**
     * Создает группу для каналов без группы
     *
     * @return int|bool id новой группы
     * @throws \Exception
     */
    private function addNonameGroup() : int
    {
        if ($this->hasNonameGroup()) {
            return $this->getNonameGroupId();
        }

        $group = new ChannelGroup();
        $group->original_name = self::NONAMEGROUP;
        $group->new_name = self::NONAMEGROUP;
        if ($group->save())
            return $group->id;
        else
            throw new \Exception('Не удалось создать группу ' . self::NONAMEGROUP);
    }

    /**
     * Удаляет группу, созданную для каналов без группы
     *
     * @return bool|int id удаляемой группы
     */
    public function destroyNonameGroup() : int
    {
        $groupId = $this->getNonameGroupId();
        if ($groupId !== false)
            return ChannelGroup::destroy($groupId);
        return false;
    }

    /**
     * Получает id группы для каналов без группы
     *
     * @return int
     */
    private function getNonameGroupId() : int
    {
        $group = ChannelGroup::where('original_name', self::NONAMEGROUP)->first();
        if (count($group))
            return $group->id;
        else
            return false;
    }

}
