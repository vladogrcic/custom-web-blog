<?php
namespace App\Containers;
use App\Repositories\MetaRepository as MetaRepo;
use App\Setting;

class SettingsContainer {
    function get() {
        $siteMetaData = [];
        $model = new MetaRepo;
        $model->metaModel = new Setting;
        $siteMetaData['title'] = $model->getMeta('title');
        $siteMetaData['description'] = $model->getMeta('description');
        $siteMetaData['image'] = $model->getMeta('featured_image');
        $siteMetaData['url'] = $model->getMeta('url');
        $siteMetaData['disable_language_group'] = $model->getMeta('disable_language_group');
        $siteMetaData['show_lang_switch'] = $model->getMeta('show_lang_switch');
        $siteMetaData['main_lang'] = $model->getMeta('main_lang');
        if(!$siteMetaData['show_lang_switch']){
            $siteMetaData['show_lang_switch'] = 'icons';
        }
        return $siteMetaData;
    }
}