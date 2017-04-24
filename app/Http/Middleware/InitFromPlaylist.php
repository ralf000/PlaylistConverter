<?php

namespace App\Http\Middleware;

use App\ChannelGroup;
use App\DBChannel;
use App\Http\Controllers\ChannelGroupController;
use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\PlaylistController;
use App\Playlist;
use Closure;
use Illuminate\Http\RedirectResponse;

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
        if ($this->checkPlaylistLink($request) === true) {
            $this->updateDataFromPlaylist();
            return $next($request);
        }
    }

    /**
     * Проверяет добавлена ли ссылка на плейлист
     * @param $request
     * @return bool|RedirectResponse
     */
    private function checkPlaylistLink($request)
    {
        if (!config('main.inputPlaylist.value') && !$request->inputPlaylist) {
            return $this->redirectToConfig('Пожалуйста заполните все поля в настройках');
        }

        if (session('inputPlaylistIsCorrect') === true)
            return true;

        if (!Playlist::inputPlaylistIsCorrect(config('main.inputPlaylist.value'))) {
            return $this->redirectToConfig('Неверная ссылка на плейлист. Измените ссылку в настройках, чтобы продолжить работу');
        } else {
            session(['inputPlaylistIsCorrect' => true]);
        }

        return true;
    }

    /**
     * Проверяет добавлены ли каналы и группы из плейлиста, указанного в настройках
     */
    private function updateDataFromPlaylist()
    {
        $groupWithOwnChannels = ChannelGroup::where('channels.own', 0)
            ->join('channels', 'channel_groups.id', '=', 'channels.group_id')
            ->get(['channel_groups.id']);
        $ownChannels = DBChannel::where('own', 0)->get();
        if (ChannelGroup::all()->isEmpty() || count($groupWithOwnChannels) === 0)
            PlaylistController::updateGroupsFromPlaylist();
        if (DBChannel::all()->isEmpty() || count($ownChannels) === 0)
            PlaylistController::updateChannelsFromPlaylist();
    }

    /**
     * Перенаправляет в раздел настроек сайта
     *
     * @param $message
     * @return RedirectResponse
     */
    private function redirectToConfig($message)
    {
        session()->flash('info', $message);
        if (\Route::currentRouteName() !== 'config')
            return redirect()->route('config');
    }
}
