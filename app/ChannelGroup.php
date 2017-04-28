<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelGroup extends Model
{
    protected $table = 'channel_groups';

    protected $fillable = ['original_name', 'new_name', 'sort', 'own'];

    public $timestamps = false;

    public function channels()
    {
        return $this->hasMany(DBChannel::class, 'group_id');
    }

    /**
     * Проверяет наличие группы в бд
     *
     * @param string $name
     * @return int|bool
     */
    public static function exists(string $name) : int
    {
        $group = self::where('new_name', $name)->first();
        return !empty($group) ? $group->id : false;
    }

}
