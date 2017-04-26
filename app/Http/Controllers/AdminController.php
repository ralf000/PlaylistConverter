<?php

namespace App\Http\Controllers;

use App\Config;
use App\Helpers\Log;
use App\TVProgram;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $title = 'Административная панель';

        $outputPlaylistPath = url('/') . '/' . Config::get('outputPlaylistName');
        $outputTVProgramPath = url('/') . '/' . Config::get('outputTVProgramName') . '.gz';

        return view('admin.index', compact('title', 'outputPlaylistPath', 'outputTVProgramPath', 'tvProgramData'));
    }
}
