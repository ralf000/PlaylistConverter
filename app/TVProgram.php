<?php

namespace App;

use App\Contracts\AFile;
use App\Contracts\ICreating;
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
            if (!$this->save($inputTVGzData))
                throw new FileException('Не удалось сохранить файл телепрограммы');
        }
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
        $xml = $this->getSimpleXml();
        if (!$xml)
            throw new FileException('Не удалось открыть файл ' . $this->outputTVPath);
        $xmlChannels = [];
        foreach ($xml as $item) {
            /**
             * @var \SimpleXMLElement $item
             */
            $xmlChannels[] = MbString::mb_trim((string)$item->{'display-name'});
        }
        $playlistChannels = (new Playlist())->getHandledChannels();
        $withoutProgram = [];
        foreach ($playlistChannels as $playlistChannel) {
            /**
             * @var Channel $playlistChannel
             */
            $playlistChannelTitle = $playlistChannel->getTitle();
            if (!in_array($playlistChannelTitle, $xmlChannels))
                $withoutProgram[] = $playlistChannelTitle;
        }
        $this->delete($this->outputTVPath);
        return $this->showChannelsWithoutProgram($withoutProgram);
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
    private function showChannelsWithoutProgram(array $withoutProgram) : string
    {
        if (empty($withoutProgram)) {
            $output = '<p><strong>Телепрограмма доступна для всех телеканалов текущего плейлиста</strong></p>';
        } else {
            $output = '<p><strong>Телепрограмма не найдена для следующих телеканалов:</strong></p>';
            $output .= '<div class="row"><ul>';
            $channels = [];
            foreach ($withoutProgram as $channel) {
                $channels[] = '<li>' . htmlspecialchars($channel) . '</li>';
            }
            if (count($channels) > 10) {
                $chunks = array_chunk($channels, ceil(count($channels) / 3));
                foreach ($chunks as $chunk) {
                    $output .= '<div class="col-md-4">' . implode("\n\t", $chunk) . '</div>';
                }
            }
            $output .= '</ul></div>';
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
}