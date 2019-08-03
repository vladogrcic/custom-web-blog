<?php
namespace App\Repositories;

use Auth;
use App\Post;
use App\Group;
use App\Tag;
use App\Media;
use App\User;
use Storage;
use App\Language;
use App\Containers\SettingsContainer as Settings;

/**
 * Repository for group items like Categories, Languages, Tags.
 *
 * @method void post(object $group, Request $request) Updates or adds group items to the database.
 *
 */
class GroupRepository
{
    /**
     * Handles group item updating or creating.
     *
     * @param object $group Instance of the Group item class.
     * @param Request $request Input from front-end in Request type.
     * @return void
     *
     */
    public function post(object $group, object $request)
    {
        $meta = (new Settings)->get();
        $metaDisLang = $meta['disable_language_group'];
        $metaMainLang = $meta['main_lang'];
        
        if ($group::where('slug', '=', $request->slug)->exists()&&$request->_method!=='PUT') {
            return;
        } else {
            $group->slug = $request->slug;
        }
        if (property_exists($request, 'id')) {
            if ($request->id !== null) {
                $group->id = $request->id;
            }
        }
        
        $group->name = $request->name;
        if (property_exists($request, 'desc')) {
            if ($request->desc) {
                $group->description = $request->desc;
            }
        }
        if (property_exists($request, 'language_id')) {
            if ($metaDisLang) {
                $group->language_id = (int)$metaMainLang;
            }
            elseif ($request->language_id) {
                $group->language_id = $request->language_id;
            }
            else{
                $group->language_id = 1;
            }
        }
        $group->save();
    }
}
