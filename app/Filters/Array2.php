<?php namespace App\Filters;

use Illuminate\Support\Str;
use Elegant\Sanitizer\Contracts\Filter;

class Array2 implements Filter
{
    public function apply($string, $options = [])
    {
        if (is_array($string)) {
            return $string;
        } else {
            if ($string) {
                return json_encode($string);
            } else {
                return [];
            }
        }
    }
}
