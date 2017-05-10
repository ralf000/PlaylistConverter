<?php

namespace App;

use App\Helpers\MbString;
use Illuminate\Database\Eloquent\Model;

class DBChannel extends Model
{
    protected $table = 'channels';

    protected $fillable = [
        'original_name', 
        'new_name', 
        'original_url', 
        'new_url',
        'original_group_id',
        'group_id',
        'sort', 
        'own'
    ];

    public $timestamps = false;

    /**
     * Связь с ChannelGroup
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(ChannelGroup::class);
    }
}