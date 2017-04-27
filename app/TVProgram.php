<?php

namespace App;

use App\Contracts\AFile;
use App\Contracts\ICreating;
use App\Helpers\Log;
use App\Helpers\MbString;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class TVProgram extends AFile implements ICreating
{
    /**
     * @var string xmlTv.xml.gz
     */
    private $outputTVName = '';
    /**
     * @var string path/xmlTv.xml
     */
    private $outputTVPath = '';
    /**
     * @var string path/xmlTv.xml.gz
     */
    private $outputTVGzPath = '';

    /**
     * TVProgram constructor.
     */
    public function __construct()
    {
        $this->path = Config::get('inputTVProgram');
        parent::__construct($this->path);
        $this->outputTVName = Config::get('outputTVProgramName');
        $this->outputTVPath = public_path() . DIRECTORY_SEPARATOR . $this->outputTVName;
        $this->outputTVGzPath = $this->outputTVPath . '.gz';
    }

    /**
     * Скачивает телепрограмму на сервер
     */
    public function create()
    {
        $inputTVGzData = file_get_contents($this->path);
        if (!$this->save($inputTVGzData) || !$this->checkCorrectlyDate()) {
            $inputTVGzData = file_get_contents($this->getReserveTvProgramPath());
            Log::log('Телепрограмма  недоступна или имеет неверную дату (' . Config::get('inputTVProgram') . '). 
            Задействована резервная телепрограмма (' . Config::get('inputReserveTVProgram') . ')');
            if (!$this->save($inputTVGzData))
                throw new FileException('Не удалось сохранить файл телепрограммы');

        }
        \Cache::forever('currentTVProgram', $this->path);
        Log::log('Телепрограмма успешно создана (' . $this->path . ')');

        $this->delete($this->outputTVPath);
    }

    private function save(string $data = '')
    {
        return ($data) ? file_put_contents($this->outputTVGzPath, $data) : false;
    }

    /**
     * Проверяет наличие телепрограммы для каналов плейлиста
     *
     * @throws FileException
     * @return string
     */
    public function check()
    {
        $this->create();
        $withoutProgram = $this->getWithoutProgramChannelsList();
        return $this->getChannelsWithoutProgram($withoutProgram);
    }

    /**
     * Получает список каналов без телепрограммы
     * Составляет рекомендации по переименованию данных каналов
     *
     * @return array
     */
    private function getWithoutProgramChannelsList() : array
    {
        $xmlChannels = $this->getXmlChannelsList();
        $playlistChannels = (new Playlist())->getHandledChannels();
        $withoutProgram = [];
        $foundChannel = ''; //канал - рекомендация
        foreach ($playlistChannels as $playlistChannel) {
            /**
             * @var Channel $playlistChannel
             */
            $playlistChannelTitle = $playlistChannel->getTitle();
            if (!in_array($playlistChannelTitle, $xmlChannels)) {
                if (in_array(mb_strtolower($playlistChannelTitle), array_map('mb_strtolower', $xmlChannels))) {
                    foreach ($xmlChannels as $xmlChannel) {
                        if (mb_strtolower($xmlChannel) == mb_strtolower($playlistChannelTitle))
                            $foundChannel = $xmlChannel;
                    }
                } else {
                    foreach ($xmlChannels as $xmlChannel) {
                        $foundChannel = $this->reduceSearch(
                            $xmlChannel,
                            $playlistChannelTitle,
                            mb_strlen($playlistChannelTitle)
                        );
                        if ($foundChannel) break;
                    }
                }
                $withoutProgram[] = ['originalTitle' => $playlistChannelTitle, 'recommendedTitle' => $foundChannel];
            }
        }
        $this->delete($this->outputTVPath);

        $this->sendReport($withoutProgram);

        return $withoutProgram;
    }

    /**
     * Получает список каналов из телепрограммы
     *
     * @return array
     */
    private function getXmlChannelsList() : array
    {
        $xml = $this->getSimpleXml();
        if (!$xml)
            throw new FileException('Не удалось открыть файл ' . $this->outputTVPath);
        $xmlChannels = [];
        foreach ($xml as $item) {
            /**
             * @var \SimpleXMLElement $item
             */
            $foundXmlChannel = MbString::mb_trim((string)$item->{'display-name'});
            if ($foundXmlChannel)
                $xmlChannels[] = $foundXmlChannel;
        }
        return $xmlChannels;
    }

    /**
     * Ищет совпадения отнимая по одному символу
     *
     * @param string $haystack
     * @param string $needle
     * @param int $needleOriginLength
     * @return string|bool
     */
    private function reduceSearch(string $haystack, string $needle, int $needleOriginLength) : string
    {
        if ($needleOriginLength < 5 || mb_strlen($needle) < 5) return false;
        if (mb_stripos($haystack, $needle) !== false)
            return $haystack;
        return $this->reduceSearch($haystack, mb_substr($needle, 0, -1), $needleOriginLength);
    }

    /**
     * @return \SimpleXMLElement
     * @throws FileException
     */
    private function getSimpleXml() : \SimpleXMLElement
    {
        $xmlTv = $this->gzUnzip();
        $simpleXml = simplexml_load_file($this->outputTVPath);
        return $simpleXml;
    }

    /**
     * @return bool
     * @throws FileException
     */
    private function checkCorrectlyDate() : bool
    {
        $dates = [];
        $xml = $this->getSimpleXml();
        if (!$xml)
            return false;
        foreach ($xml as $item) {
            /**
             * @var \SimpleXMLElement $item
             */
            if (!$item->{'title'})
                continue;
            $dates[] = mb_substr($item->attributes()->start, 0, 8);
        }
        $minDate = new \DateTime(min($dates));
        $maxDate = new \DateTime(max($dates));
        $now = new \DateTime();
        if (($now < $maxDate) && ($now > $minDate))
            return true;
        return false;
    }

    /**
     * @return string
     */
    private function getReserveTvProgramPath() : string
    {
        return $this->path = Config::get('inputReserveTVProgram');
    }

    /**
     * Отображает каналы без телепрограммы
     * @param array $withoutProgram
     * @return string
     */
    private function getChannelsWithoutProgram(array $withoutProgram) : string
    {
        if (empty($withoutProgram)) {
            $output = '<p><strong>Телепрограмма доступна для всех телеканалов текущего плейлиста</strong></p>';
        } else {
            $output = '<p><strong>Телепрограмма не найдена для следующих телеканалов:</strong></p>';
            $output .= '<div class="row">';
            $channels = [];
            foreach ($withoutProgram as $channel) {
                $channels[] = "<tr>\n<td>" . htmlspecialchars($channel['originalTitle']) . "</td>\n"
                    . '<td class="gray">' . htmlspecialchars($channel['recommendedTitle']) . "</td>\n</tr>\n";
            }
            if (count($channels) > 10) {
                $chunks = array_chunk($channels, ceil(count($channels) / 3));
                foreach ($chunks as $chunk) {
                    $output .= '<div class="col-md-4">'
                        . "\n<table class='table table-bordered table-striped'>\n"
                        . "<tr>\n<th>Название</th>\n<th>Рекомендуемое название</th>\n</tr>\n"
                        . implode("\n\t", $chunk)
                        . "\n</table>\n</div>";
                }
            }
            $output .= "\n</div>\n";
        }
        return $output;
    }

    /**
     * @throws FileException
     */
    private function gzUnzip()
    {
        $tvInput = gzopen($this->outputTVGzPath, 'r');
        $tvOutput = fopen($this->outputTVPath, 'w');
        if (!$tvInput || !$tvOutput)
            throw new FileException('Не удалось открыть один или несколько файлов телепрограммы');
        while (($line = fgets($tvInput)) !== FALSE) {
            fwrite($tvOutput, $line);
        }
        $this->close($tvInput);
        $this->close($tvOutput);
        return true;
    }

    /**
     * Посылает рапорт о проверке телепрограммы
     *
     * @param array $withoutProgramChannels каналы без телепрограммы
     */
    private function sendReport(array $withoutProgramChannels)
    {
        $numChannelsWithRecommendationsNum = 0;
        foreach ($withoutProgramChannels as $withoutProgramChannel) {
            if ($withoutProgramChannel['recommendedTitle'])
                $numChannelsWithRecommendationsNum++;
        }
        Log::log('Телепрограмма успешно проверена. Для ' . count($withoutProgramChannels) . ' каналов не найдено телепрограммы. Для ' . $numChannelsWithRecommendationsNum . ' каналов подготовлены рекомендации по переименованию для корректной работы телепрограммы.');
    }
}