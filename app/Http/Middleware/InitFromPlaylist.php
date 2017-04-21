<?php

namespace App\Http\Middleware;

use App\ChannelGroup;
use App\DBChannel;
use App\Http\Controllers\ChannelGroupController;
use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\PlaylistController;
use Closure;

class InitFromPlaylist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * Проверяет добавлена ли ссылка на плейлист
         */
        if (!config('main.inputPlaylist.value') && !$request->inputPlaylist) {
            session()->flash('info', 'Пожалуйста заполните все поля в настройках');
            if (\Route::currentRouteName() !== 'config')
                return redirect()->route('config');
        }
        /**
         * Проверяет добавлены ли каналы и группы из плейлиста, указанного в настройках
         */
        $groupWithOwnChannels = ChannelGroup::where('channels.own', 0)
            ->join('channels', 'channel_groups.id', '=', 'channels.group_id')
            ->get(['channel_groups.id']);
        $ownChannels = DBChannel::where('own', 0)->get();
        if (ChannelGroup::all()->isEmpty() || count($groupWithOwnChannels) === 0)
            PlaylistController::updateGroupsFromPlaylist();
        if (DBChannel::all()->isEmpty() || count($ownChannels) === 0)
            PlaylistController::updateChannelsFromPlaylist();

        return $next($request);
    }
}
