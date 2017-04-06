<?php

namespace App;

use App\Helpers\MbString;
use Illuminate\Database\Eloquent\Model;

class DBChannel extends Model
{
    protected $table = 'channels';

    protected $fillable = ['original_name', 'new_name', 'sort', 'own', 'group_id'];

    public $timestamps = false;

    /**
     * Связь с ChannelGroup
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function filter()
    {
        return $this->belongsTo(ChannelGroup::class);
    }
}