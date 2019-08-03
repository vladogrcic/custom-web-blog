<?php namespace App\Filters;

use Illuminate\Support\Str;
use Waavi\Sanitizer\Contracts\Filter;
use Purifier;

class StripDangerTags implements Filter
{
    public function apply($string, $options = [])
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('HTML.SafeIframe', true);

        // Set some HTML5 properties
        $config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
        $config->set('HTML.DefinitionRev', 1);
        $config->set('HTML.Allowed', 'div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[ width|height|alt|src ],h1,h2,h3,h4,h5,h6,figcaption[class],figure[class],iframe[src|style|frameborder|allowfullscreen],oembed[url]');
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,width');
        $config->set('HTML.AllowedAttributes', 'class, src, height, width, alt, iframe.allowfullscreen');

        if ($def = $config->maybeGetRawHTMLDefinition()) {
            // http://developers.whatwg.org/the-video-element.html#the-video-element
            // $def->addAttribute('iframe', 'allowfullscreen', 'bool');
            $def->addElement('img', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
                'src'      => 'URI',
                'type'     => 'Text',
                'width'    => 'Length',
                'height'   => 'Length',
                'poster'   => 'URI',
                'preload'  => 'Enum#auto,metadata,none',
                'controls' => 'Bool',
            ));
            $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
                'src'      => 'URI',
                'type'     => 'Text',
                'width'    => 'Length',
                'height'   => 'Length',
                'poster'   => 'URI',
                'preload'  => 'Enum#auto,metadata,none',
                'controls' => 'Bool',
            ));
            $def->addElement('iframe', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
                'src'      => 'URI',
                'allowfullscreen' => 'Bool',
            ));
            $def->addElement('figcaption', 'Block', 'Flow', 'Common');
            $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
            $def->addElement('oembed', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
                'url'     => 'URI',
            ));
        }
        $purifier = new \HTMLPurifier($config);
        $purified = $purifier->purify($string);
        return $purified;
    }

}
