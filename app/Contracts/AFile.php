<?php

namespace App\Contracts;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Class AFile
 * @package app\classes
 */
abstract class AFile
{
    /**
     * @var string
     */
    protected $path = '';
    /**
     * @var bool|null|resource
     */
    protected $descriptor = null;

    /**
     * AFile constructor.
     * @param string $path
     * @throws FileNotFoundException
     */
    public function __construct($path)
    {
        if (!is_null($this->descriptor))
            return null;

        if (!$this->descriptor = fopen($path, 'r'))
            throw new FileNotFoundException('Не удалось открыть файл');
        $this->path = $path;
    }

    /**
     * @param $descriptor
     * @return bool
     */
    protected function close($descriptor) : bool
    {
        return fclose($descriptor);
    }

    /**
     * @param $path
     * @return bool
     */
    protected function delete($path) : bool
    {
        return unlink($path) ? true : false;
    }
}