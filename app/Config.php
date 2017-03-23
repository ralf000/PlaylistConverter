<?php

namespace App;

use Infinety\Config\Rewrite;

class Config
{
    /**
     * @var string имя используемого файла конфига
     */
    private $configName = 'main';

    /**
     * @var array данные класса
     */
    private $data = [];

    /**
     * @return array
     */
    public function all() : array
    {
        return config($this->configName);
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function update(array $data)
    {
        $this->fill($data);
        $data = $this->prepareToSave();
        $mainConfig = new Rewrite();
        $mainConfig->toFile(config_path() . '/main.php', $data);
    }

    /**
     * @param array $data
     */
    private function fill(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function prepareToSave() : array
    {
        if (!$this->data || !is_array($this->data))
            throw new \Exception('Данные для сохранения отсутствуют или не являются массивом');

        $output = [];
        foreach ($this->data as $key => $item) {
            $output[$key . '.value'] = $item;
        }
        return $output;
    }
}
