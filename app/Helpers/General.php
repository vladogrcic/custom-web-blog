<?php
namespace App\Helpers\General;

class GeneralHelper
{
    /**
     * Display bytes as needed units.
     * @param  number $bytes Bytes that need converting.
     * @return string Final form of it.
     */
    public static function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
    /**
     * Get a random string of letters.
     *
     * @param  number $len Number of letters needed.
     * @return string Final string of letters.
     */
    public static function getRandomWord($len = 10)
    {
        $word = array_merge(range('a', 'z'), range('A', 'Z'));
        shuffle($word);
        return substr(implode($word), 0, $len);
    }
    /**
     * Converts an array to the utf8 encoding.
     *
     * @param  array $dat Array to convert.
     * @return array Exports the converted array.
     */
    public static function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) {
                $ret[ $i ] = self::convert_from_latin1_to_utf8_recursively($d);
            }
            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d) {
                $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);
            }
            return $dat;
        } else {
            return $dat;
        }
    }
}
