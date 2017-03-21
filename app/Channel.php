<?php

namespace App;

use App\Helpers\MbString;

class Channel 
{
    /**
     * @var array [title => 'title', group => 'group', url => 'url']
     */
    private $channel = [];
    /**
     * @var string Шаблон для создания плейлиста
     */
    private $template = '#EXTINF:0 group-title="{group}",{title}' . PHP_EOL . '{url}' . PHP_EOL;

    /**
     * Channel constructor.
     * @param array $channel
     * [title => 'title', group => 'group', url => 'url']
     */
    public function __construct(array $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Конвертирует канал для создания плейлиста
     * @return string
     */
    public function convert() : string
    {
        return strtr($this->template, [
            '{group}' => MbString::mb_ucfirst($this->channel['group']),
            '{title}' => $this->channel['title'],
            '{url}' => $this->channel['url'],
        ]);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->channel['title'];
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->channel['group'];
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->channel['title'] = $title;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->channel['group'] = $group;
    }
}