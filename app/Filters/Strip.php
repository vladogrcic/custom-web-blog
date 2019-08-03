<?php namespace App\Filters;

use Illuminate\Support\Str;
use Waavi\Sanitizer\Contracts\Filter;

class Strip implements Filter
{
    private $illegals = [
        '\'' => ' ',  
        '?' => '',
        ';' => '',
        '--' => '-',
        '<' => '',
        '>' => '',
        '/' => '',
        '&' => ' and'
    ];
    public function apply($string, $options = [])
    {
        foreach($this->illegals as $illegal => $replacement) {
            $string = str_replace(
                $illegal,
                $replacement ?: '',
                $string
            );
        }
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                "—", "–", ",", "<", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = substr($clean, 0);
        return strtolower(mb_strtolower($clean, 'UTF-8'));
    }
}
