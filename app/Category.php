<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function Posts(){
        return $this->belongsToMany('App\Post');
    }
    public function Language($value='')
    {
        return $this->belongsTo('App\Language', 'language_id');
    }
}
