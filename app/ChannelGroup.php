<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelGroup extends Model
{
    protected $table = 'channel_groups';

    protected $fillable = ['name', 'sort'];

    public $timestamps = false;
}
