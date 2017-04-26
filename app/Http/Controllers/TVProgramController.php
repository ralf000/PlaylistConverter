<?php

namespace App\Http\Controllers;

use App\TVProgram;
use Illuminate\Support\Facades\Request;

class TVProgramController extends Controller
{
    /**
     * Возвращает список телеканалов, для которых не найдена телепрограмма
     * Использует кэширование
     */
    public function getNotFoundChannels()
    {
        if (filter_has_var(INPUT_GET, 'reset-cache')) {
            \Cache::forget('channelsWithoutTVProgram');
        }

        $tvProgramData = \Cache::get('channelsWithoutTVProgram');
        if (!$tvProgramData) {
            $tvProgram = new TVProgram();
            $channels = $tvProgram->check();
            $date = date('d-m-Y H:i:s');
            $tvProgramData = ['date' => $date, 'channels' => $channels];
            \Cache::forever('channelsWithoutTVProgram', $tvProgramData);
        }
        return $tvProgramData;
    }
}
