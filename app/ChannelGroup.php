<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelGroup extends Model
{
    protected $table = 'channel_groups';

    protected $fillable = ['original_name', 'new_name', 'sort'];

    public $timestamps = false;
}
