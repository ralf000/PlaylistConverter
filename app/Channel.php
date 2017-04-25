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
     * @var string название канала
     */
    private $title = '';
    /**
     * @var string группа канала
     */
    private $group = '';
    /**
     * @var string ссылка на канал
     */
    private $url = '';
    /**
     * @var string Шаблон для создания плейлиста
     */
    private $template = '#EXTINF:0 group-title="{group}",{title}' . PHP_EOL . '{url}' . PHP_EOL;

    /**
     * DBChannel constructor.
     * @param array $channel
     * [title => 'title', group => 'group', url => 'url']
     */
    public function __construct(array $channel = [])
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
     * Получает название [и группу] канала из #EXTINF строки плейлиста
     * Examples:
     * #EXTINF:0,РБК-ТВ
     * #EXTINF:-1 tvg-name="Первый_канал" tvg-shift=0 aspect-ratio=16:9 group-title="Эфирные",Первый канал
     * #EXTINF:-1,ISR: CANAL 24
     * #EXTINF:-1,24 Техно
     * #EXTINF:0 group-title="Новости",Первый HD 720
     *
     * @param string $EXTINF
     * @return string
     */
    public function EXTINFConverter(string $EXTINF)
    {
        $EXTINFchunks = explode(',', $EXTINF);
        if (!$EXTINFchunks) return false;

        $title = MbString::mb_trim(last($EXTINFchunks));
        if ($isrPos = mb_strpos($title, 'ISR:') !== false)
            $title = MbString::mb_trim(substr($title, mb_strlen('ISR:')));
        if (preg_match('~group-title="(.*)"~Uui', $EXTINF, $groupString)) {
            $this->group = $groupString[1];
        }
        $this->title = $title;
    }

    /**
     * Получает группу канала из #EXTGRP строки плейлиста
     * Examples:
     * #EXTGRP:новости
     *
     * @param string $EXTGRP
     * @return string
     */
    public function EXTGRPConverter(string $EXTGRP) : string
    {
        $EXTGRPchunks = explode(':', $EXTGRP);
        if (!$EXTGRPchunks) return false;

        $group = MbString::mb_trim(last($EXTGRPchunks));
        return $this->group = $group;
    }

    /**
     * Получает ссылку на канал
     * Examples:
     * http://185.10.211.10:7071/play/a08u?auth=PredvTest:PredvTestFDFF465DFF
     * 
     * @param string $url
     * @return string
     */
    public function setUrl(string $url) : string
    {
        return $this->url = $url;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }


}