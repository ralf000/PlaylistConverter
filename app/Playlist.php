<?php

namespace App;


use App\Contracts\AFile;
use App\Contracts\ICreating;
use App\Helpers\ArrayHelper;
use App\Helpers\Log;
use App\Helpers\MbString;

class Playlist extends AFile implements ICreating
{
    /**
     * @var int количество каналов, полученных из плейлиста
     */
    private $rawChannelsNum = 0;
    /**
     * @var int количество каналов после обработки
     */
    private $handledChannelsNum = 0;

    /**
     * @var array информация о количестве каналов для логирования:
     * $channelsInfo['raw'] int количество каналов, полученных из плейлиста
     * $channelsInfo['handled'] int количество каналов после обработки
     * $channelsInfo['own'] int количество каналов, добавленных пользователем
     */
    private $channelsInfo = [
        'raw' => 0,
        'handled' => 0,
        'own' => 0
    ];

    const EXTINF = '#EXTINF';
    const EXTGRP = '#EXTGRP';
    const URL_SCHEME = 'http';

    /**
     * @var array массив необработанных каналов типа Channel
     */
    private $channels = [];

    /**
     * Playlist constructor.
     */
    public function __construct()
    {
        $path = Config::get('inputPlaylist');
        parent::__construct($path);
    }

    /**
     * Создает новый плейлист
     */
    public function create()
    {
        $this->setChannelsFromPlaylist();
        $this->handleChannels();
        $this->writeDataToPlaylist();
    }

    /**
     * Проверяет корректность плейлиста
     * @param string $url ссылка на плейлист
     * @return bool
     */
    public static function inputPlaylistIsCorrect(string $url) : bool
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
     * Вовзращает полный путь до выходного плейлиста
     *
     * @return string
     */
    public static function getPath()
    {
        $playlistName = Config::get('outputPlaylistName');
        return public_path() . DIRECTORY_SEPARATOR . $playlistName;
    }

    /**
     * Парсит телеканалы из плейлиста
     */
    private function setChannelsFromPlaylist()
    {
        $skipNextTitle = false; //пропустить следующую строку
        $channel = new Channel(); //инициализация канала
        while (!feof($this->descriptor)) {
            $line = MbString::mb_trim(fgets($this->descriptor));
            if (empty($line) || mb_substr($line, 0, 7) == '#EXTM3U')
                continue;
            if (mb_strpos($line, 'tvg-logo') !== false) {
                $skipNextTitle = true;
                continue;
            }
            if ($skipNextTitle) {
                $skipNextTitle = false;
                continue;
            }

            if (mb_substr($line, 0, 7) == self::EXTINF) {
                $channel->EXTINFConverter($line);
            } else if (mb_substr($line, 0, 7) == self::EXTGRP) {
                $channel->EXTGRPConverter($line);
            } else if (mb_substr($line, 0, 4) == self::URL_SCHEME) {
                $channel->setUrl($line);
                $this->channels[] = $channel;
                $channel = new Channel();
            }
        }
        $this->channelsInfo['raw'] = count($this->channels);

        $this->close($this->descriptor);
    }

    private function handleChannels()
    {
        foreach ($this->channels as $key => $channel) {
            /**
             * @var Channel $channel
             */
            $dbChannel = DBChannel::where('new_url', $channel->getUrl())->first();
            if (is_null($dbChannel)) continue;
            if ($dbChannel->group->hidden) unset($this->channels[$key]);
            if ($dbChannel->hidden) unset($this->channels[$key]);
            $channel->fill($dbChannel);
        }
        $this->addOwnChannels();
        $this->sort();

        $this->channelsInfo['handled'] = count($this->channels);
    }

    /**
     * Записывает сформированные каналы в файл плейлиста
     */
    private function writeDataToPlaylist()
    {
        $playlistPath = self::getPath();
        $descriptor = fopen($playlistPath, 'w');
        fwrite($descriptor, '#EXTM3U' . PHP_EOL);
        foreach ($this->channels as $channel) {
            /**
             * @var Channel $channel
             */
            fwrite($descriptor, $channel->convert());
        }
        $this->sendReport();
        
        $this->close($descriptor);
    }

    /**
     * Добавляет дополнительные каналы
     */
    private function addOwnChannels()
    {
        $additionalChannels = DBChannel::where('own', 1)->get();
        foreach ($additionalChannels as $addChannel) {
            if ($addChannel->group->hidden || $addChannel->hidden) continue;

            $channel = new Channel();
            $channel->fill($addChannel);
            $this->channels[] = $channel;

            $this->channelsInfo['own']++;
        }
    }

    /**
     * Сортирует группы каналов
     *
     * @return bool
     */
    private function sort()
    {
        return usort($this->channels, function ($a, $b) {
            /**
             * @var Channel $a
             * @var Channel $b
             */
            if ($a->getGroupPosition() > $b->getGroupPosition())
                return 1;
            if ($a->getGroupPosition() < $b->getGroupPosition())
                return -1;
            if ($a->getGroupPosition() == $b->getGroupPosition()) {
                return $a->getPosition() <=> $b->getPosition();
            }
        });
    }

    private function sendReport()
    {
        Log::log("Новый плейлист успешно создан. Всего каналов: {$this->channelsInfo['raw']}. 
        После обработки: {$this->channelsInfo['handled']}. Пользовательских каналов: {$this->channelsInfo['own']}");
    }

    /**
     * Возвращает список необработанных каналов из плейлиста
     *
     * @return array
     */
    public function getRawChannels() : array
    {
        $this->setChannelsFromPlaylist();
        return $this->channels;
    }

    /**
     * Возвращает список обработанных каналов из плейлиста
     *
     * @return array
     */
    public function getHandledChannels() : array
    {
        $this->setChannelsFromPlaylist();
        $this->handleChannels();
        return $this->channels;
    }

}