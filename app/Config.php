<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';

    protected $fillable = ['name', 'value'];

    public $timestamps = false;

    private $configFields = [
        'builderMode',
        'inputPlaylist',
        'outputPlaylistName',
        'inputTVProgram',
        'inputReserveTVProgram',
        'outputTVProgramName'
    ];

    /**
     * @return array
     */
    public static function getConfigFields() : array
    {
        return (new self)->configFields;
    }
    
}
