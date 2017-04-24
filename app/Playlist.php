<?php

namespace App;


use App\Contracts\AFile;
use App\Contracts\ICreating;
use App\Helpers\ArrayHelper;
use App\Helpers\MbString;

class Playlist extends AFile implements ICreating
{

    const EXTINF = '#EXTINF';
    const EXTGRP = '#EXTGRP';
    const URL_SCHEME = 'http';
    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var array массив обработанных каналов типа Channel
     */
    private $channels = [];

    /**
     * Playlist constructor.
     */
    public function __construct()
    {
        $path = config('main.inputPlaylist.value');
        parent::__construct($path);
    }

    /**
     * Создает новый плейлист
     */
    public function create()
    {
        $this->handleChannels();
        $this->writeDataToPlaylist();
    }

    /**
     * Проверяет корректность плейлиста
     * @param string $url ссылка на плейлист
     * @return bool
     */
    public static function inputPlaylistIsCorrect($url) : bool
    {
        $descriptor = @fopen($url, 'r');
        if (!$descriptor)
            return false;

        while (!feof($descriptor)) {
            $line = MbString::mb_trim(fgets($descriptor));
            if (empty($line)) continue;
            if (mb_substr($line, 0, 7) == '#EXTM3U' || mb_substr($line, 0, 7) == '#EXTINF')
                return true;
        }
        return false;
    }

    /**
     * Возвращает группы из плейлиста
     *
     * @return array
     */
    public function getGroupsFromPlaylist()
    {
        $this->setChannelsFromPlaylist();
        $groups = [];
        /**
         * @var Channel $channel
         */
        foreach ($this->channels as $channel) {
            $groups[] = $channel->getGroup();
        }
        return array_values(array_unique($groups));
    }

    /**
     * Парсит телеканалы из плейлиста
     */
    private function setChannelsFromPlaylist()
    {
        $skipNextTitle = false; //пропустить следующую строку
        while (!feof($this->descriptor)) {
            $line = MbString::mb_trim(fgets($this->descriptor));
            if (empty($line) || mb_substr($line, 0, 7) == '#EXTM3U')
                continue;
            if (mb_strpos($line, 'tvg-logo') !== false) {
                $skipNextTitle = true;
                continue;
            }
            if ($skipNextTitle){
                $skipNextTitle = false;
                continue;
            }

            $channel = new Channel();
            if (mb_substr($line, 0, 7) == self::EXTINF) {
                $channel->EXTINFConverter($line);
            } else if (mb_substr($line, 0, 7) == self::EXTGRP) {
                $channel->EXTGRPConverter($line);
            } else if (mb_substr($line, 0, 4) == self::URL_SCHEME) {
                $channel->setUrl($line);
                $this->channels[] = $channel;
            }
        }
        $this->close($this->descriptor);
    }

    private function handleChannels()
    {
        $this->channels = [];
        foreach ($this->channels as $channel) {
            $this->channel = $channel;
            $this->changeChannelAttribute();
            if ($this->filterChannel()) {
                $this->channels[] = $this->channel;
            }
        }
        $this->addAdditionalChannels();
        $this->sortChannels();
        $this->sortGroups();
    }

    /**
     * Записывает сформированные каналы в файл плейлиста
     */
    private function writeDataToPlaylist()
    {
        $playlistName = config('main.outputPlaylistName');
        $playlistPath = __DIR__ . '/../../' . $playlistName;
        $descriptor = fopen($playlistPath, 'w');
        fwrite($descriptor, '#EXTM3U' . PHP_EOL);
        foreach ($this->channels as $channel) {
            /**
             * @var Channel $channel
             */
            fwrite($descriptor, $channel->convert());
        }
        $this->close($descriptor);
        App::get('logger')->successCreatePlaylistLog($this->channelCounter, count($this->channels));
    }

    /**
     * Переименовывает каналы и меняет их группы
     */
    private function changeChannelAttribute()
    {
        $title = $this->channel->getTitle();
//        $renameChannels = $this->config->get('renameChannels');
        $renameChannels = RenamedChannel::all()->toArray();
        if (array_key_exists($title, $renameChannels))
            $this->channel->setTitle($renameChannels[$title]);
//        $changeGroups = ArrayHelper::arrayValuesChangeCase($this->config->get('changeGroups'));
        $changeGroups = ArrayHelper::arrayValuesChangeCase(ChangedGroupChannel::all()->toArray());
        if (array_key_exists($title, $changeGroups))
            $this->channel->setGroup($changeGroups[$title]);
    }

    /**
     * Фильтрует каналы
     * @return bool
     */
    private function filterChannel() : bool
    {
//        $excludeChannels = $this->config->get('excludeChannels');
        $excludeChannels = ExcludedChannel::all()->toArray();
        if (in_array($this->channel->getTitle(), $excludeChannels))
            return false;
//        $excludeGroups = ArrayHelper::arrayValuesChangeCase($this->config->get('excludeGroups'));
        $excludeGroups = ArrayHelper::arrayValuesChangeCase(ExcludedGroup::all()->toArray());
        if (in_array($this->channel->getGroup(), $excludeGroups))
            return false;
        return true;
    }

    /**
     * Добавляет дополнительные каналы
     */
    private function addAdditionalChannels()
    {
//        $additionalChannels = $this->config->get('additionalChannels');
        $additionalChannels = AdditionalChannel::all()->toArray();
        foreach ($additionalChannels as $additionalChannel) {
            if (!isset($additionalChannel['group']) || empty($additionalChannel['group']))
                $additionalChannel['group'] = 'другое';
            $this->channels[] = new Channel($additionalChannel);
        }
    }

    /**
     * Сортирует каналы
     * @param $direction
     * @return bool
     */
    private function sortChannels($direction = SORT_ASC)
    {
        return usort($this->channels, function ($a, $b) use ($direction) {
            /**
             * @var Channel $a
             * @var Channel $b
             */
            if ($direction === SORT_ASC)
                return $a->getTitle() <=> $b->getTitle();
            else
                return $b->getTitle() <=> $a->getTitle();
        });
    }

    private function sortGroups()
    {
        $output = [];
//        foreach ($this->config->get('groupOrder') as $group) {
        dd(SortedGroup::all()->toArray());
        foreach (SortedGroup::all()->toArray() as $group) {
            foreach ($this->channels as $channel) {
                /**
                 * @var Channel $channel
                 */
                if (mb_strtolower($channel->getGroup() === mb_strtolower($group)))
                    $output[] = $channel;
            }
        }
        $this->channels = $output;
    }

    /**
     * @return array
     */
    public function getChannels() : array
    {
        $this->setChannelsFromPlaylist();
        return $this->channels;
    }

}