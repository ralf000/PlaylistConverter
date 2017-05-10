<?php

namespace App\Helpers;


class ArrayHelper
{
    /**
     * Сравнивает 2 массива
     *
     * @param array $array1
     * @param array $array2
     * @return bool
     */
    public static function hasDiff(array $array1, array $array2) : bool
    {
        return (array_diff($array1, $array2)) || (array_diff($array2, $array1));
    }
}