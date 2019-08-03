<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function Posts(){
        return $this->belongsToMany('App\Post');
    }
    public function language($value='')
    {
        return $this->belongsTo('App\Language', 'language_id');
    }
}
