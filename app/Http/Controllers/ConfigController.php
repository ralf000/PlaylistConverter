<?php

namespace App\Http\Controllers;

use App\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * @param Config $config
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Config $config)
    {
        $title = 'Настройки приложения';
        $data = $config->all();
        return view('admin.config', compact('title', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Config $config
     * @return \Illuminate\Http\Response
     */
    public function update(Config $config, Request $request)
    {
        $input = $request->except('_token');

        $validator = \Validator::make($input, [
            'inputPlaylist' => 'required|url',
            'outputPlaylistName' => 'required|regex:~\.m3u$~',
            'inputTVProgram' => 'required|url',
            'inputReserveTVProgram' => 'required|url',
            'outputTVProgramName' => 'required|regex:~\.xml$~',
        ]);
        if ($validator->fails()) {
            return redirect()->route('config')->withInput($input)->withErrors($validator);
        }

        $config->update($input);
        return redirect()->route('config')->with('status', 'Настройки успешно обновлены');
    }
}
