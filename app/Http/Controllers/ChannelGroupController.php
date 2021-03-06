<?php

namespace App\Http\Controllers;

use App\Channel;
use App\ChannelGroup;
use App\DBChannel;
use App\Helpers\ArrayHelper;
use App\Helpers\Log;
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
        $input = $request->except(['_token', '_method']);

        $validator = \Validator::make($input, [
            'original_name' => 'required|unique:channel_groups',
        ]);

        if ($validator->fails()) {
            return redirect()->route('channels')->withErrors($validator);
        }

        if ($this->saveGroup($input) !== false)
            return redirect()->route('channels')->with('status', 'Новая группа успешно добавлена');

        throw new \Exception('При добавлении новой группы что-то пошло не так');
    }

    /**
     * Сохранет группу в базу данных
     *
     * @param array $data
     * @return bool|mixed
     */
    public function saveGroup(array $data)
    {
        //масимальное значение поля sort для сортировки новых добавляемых групп
        $maxSortValue = ChannelGroup::all('sort')->max('sort');

        $group = new ChannelGroup();
        $group->fill($data);
        $group->new_name = $group->original_name;
        $group->sort = ++$maxSortValue;
        $group->own = 1;
        if ($group->save()) {
            Log::log("Добавлена новая группа: «{$group->original_name}»");
            return $group->id;
        }
        return false;
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

            $group = ChannelGroup::find($groupData['id']);

            if (!ArrayHelper::hasDiff($group->toArray(), $groupData))
                continue;

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
            $group->fill($groupData);
            $group->update();
        }

        if ($this->emptyNonameGroup())
            $this->destroyNonameGroup();
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
        if (!$group)
            throw new \Exception("Не удалось удалить группу с идентификатором {$request->id}");

        $this->changeGroupForDeleteChannels($group->id);

        if ($this->emptyNonameGroup())
            $this->destroyNonameGroup();

        ChannelGroup::destroy($group->id);

        Log::log("Группа «{$group->new_name}» успешно удалена. 
            Все каналы данной группы были перемещены в группу «" . self::NONAMEGROUP . '»');

        return redirect()->route('channels')->with('status', 'Группа успешно удалена');
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
        if ($group->hidden === 0) {
            $group->hidden = 1;
            Log::log("Группа «{$group->new_name}» стала видимой");
        } else {
            Log::log("Группа «{$group->new_name}» скрыта");
            $group->hidden = 0;
        }
        return $group->save();
    }

    /**
     * Получает id для переданного канала
     * Создает новую группу при необходимости
     *
     * @param Channel $channel
     * @return int
     * @throws \Exception
     */
    public function getGroupIdForInputChannel(Channel $channel) : int
    {
        if ($group = $channel->getGroup()) {
            $groupId = ChannelGroup::exists($group);
            if (!$groupId) {
                $groupData = ['original_name' => $channel->getGroup()];
                $groupId = $this->saveGroup($groupData);
            }
        } else {
            $groupId = $this->addNonameGroup();
        }
        return $groupId;
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
            $channelFromGroup->original_group_id = $nonameGroupId;
            $channelFromGroup->save();

            Log::log('Группа канала «' . $channelFromGroup->new_name . '» изменена на «' . self::NONAMEGROUP . '»');
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
        $nonameGroupChannels = DBChannel::where('group_id', $nonameGroupId)->get();
        return count($nonameGroupChannels) === 0;
    }

    /**
     * Создает группу для каналов без группы
     *
     * @return int|bool id новой группы
     * @throws \Exception
     */
    public function addNonameGroup() : int
    {
        if ($this->hasNonameGroup()) {
            return $this->getNonameGroupId();
        }

        $group = new ChannelGroup();
        $group->original_name = self::NONAMEGROUP;
        $group->new_name = self::NONAMEGROUP;
        if ($group->save()) {
            Log::log('Группа «' . self::NONAMEGROUP . '» успешно создана');
            return $group->id;
        } else {
            throw new \Exception('Не удалось создать группу ' . self::NONAMEGROUP);
        }
    }

    /**
     * Проверяет наличие группы для каналов без группы и каналов в ней
     *
     * @return bool
     */
    private function hasNonameGroup() : bool
    {
        $groupId = $this->getNonameGroupId();
        if ($groupId == false) return false;

        return true;
    }

    /**
     * Удаляет группу, созданную для каналов без группы
     *
     * @return bool|int id удаляемой группы
     */
    public function destroyNonameGroup() : int
    {
        $groupId = $this->getNonameGroupId();
        if ($groupId !== false) {
            return ChannelGroup::destroy($groupId);
        }
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
        if (isset($group->id) && is_int($group->id))
            return $group->id;
        else
            return false;
    }

}
