<?php

namespace App\Http\Controllers;

use App\Playlist;
use App\TVProgram;

class IndexController extends Controller
{
    public function index()
    {
        $playlist = new Playlist();
        $playlist->create();
        $tvProgram = new TVProgram();
        $tvProgram->create();
    }
}
