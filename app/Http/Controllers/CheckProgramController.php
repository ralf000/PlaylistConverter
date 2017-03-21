<?php

namespace App\Http\Controllers;

use App\TVProgram;

class CheckProgramController extends Controller
{
    public function index()
    {
        $tvProgram = new TVProgram();
        $tvProgram->check();
    }
}
