<?php

namespace App\Http\Controllers;

use App\Config;
use App\Playlist;
use Illuminate\Http\Request;

class ConfigController extends Controller
{

    public function index(Config $config)
    {
        $title = 'Настройки приложения';
        $config = $this->prepareConfigData($config->all()->toArray());
        return view('admin.config', compact('title', 'config'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->except('_token');
        $input['builderMode'] = isset($input['builderMode']) ? 1 : 0;

        $rules = [
            'outputPlaylistName' => 'required|regex:~\.m3u$~',
            'inputTVProgram' => 'required|url',
            'inputReserveTVProgram' => 'required|url',
            'outputTVProgramName' => 'required|regex:~\.xml$~',
        ];
        if (!$input['builderMode'])
            $rules += ['inputPlaylist' => 'required|url'];
        else
            unset($input['inputPlaylist']);
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->route('config')->withInput($input)->withErrors($validator);
        }

        if (!isset($input['inputPlaylist']))
            $input['inputPlaylist'] = Config::get('inputPlaylist');

        if (!$input['builderMode'] && !Playlist::inputPlaylistIsCorrect($input['inputPlaylist'])) {
            session()->flash('info', 'Неверная ссылка на плейлист');
            return redirect()->route('config');
        }

        foreach ($input as $name => $value) {
            $configRow = Config::where('name', $name);
            $configRow->update(['name' => $name, 'value' => $value]);
        }
        return redirect()->route('config')->with('status', 'Настройки успешно обновлены');
    }

    /**
     * Дополняет выборку пустыми элементами для построения формы
     *
     * @param array $data
     * @return array
     */
    private function prepareConfigData(array $data) : array
    {
        $output = [];
        foreach ($data as $item) {
            $output[$item['name']] = $item['value'];
        }
        return $output;
    }

}
