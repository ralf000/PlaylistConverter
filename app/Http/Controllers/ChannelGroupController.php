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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $groups = ChannelGroup::all()->sortBy('sort')->toArray();
        $title = 'Группы каналов';
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
        //масимальное значение поля sort для сортировки новых добавляемых групп
        $maxSortValue = ChannelGroup::all('sort')->max('sort');

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
        $group->sort = ++$maxSortValue;
        $group->own = 1;
        if ($group->save()) {
            return redirect()->route('groups')->with('status', 'Новая группа успешно добавлена');
        }

        throw new \Exception('При добавлении новой группы что-то пошло не так');
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

            if (!$groupData['disabled']) {
                $validator = \Validator::make($groupData, [
                    'new_name' => "required|unique:channel_groups,new_name,{$groupData['id']}"
                ]);
                if ($validator->fails()) {
                    return redirect()->route('groups')->withErrors($validator);
                }
            }
            $group = ChannelGroup::find($groupData['id']);
            $group->fill($groupData);
            $group->update();
        }
        return redirect()->route('groups')->with('status', 'Изменения успешно сохранены');
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
        //если группа добавлена пользователем (own === 1) и передан верный id
        if ($group && $group->own) {
            ChannelGroup::destroy($group->id);
            return redirect()->route('groups')->with('status', 'Группа успешно удалена');
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

}
