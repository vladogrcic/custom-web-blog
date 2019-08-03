<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function user($value='')
    {
        return $this->belongsTo('App\User', 'author_id');
    }
    public function categories($value='')
    {
        return $this->belongsToMany('App\Category');
    }
    public function language($value='')
    {
        return $this->belongsTo('App\Language', 'language_id');
    }
    public function tags($value='')
    {
        return $this->belongsToMany('App\Tag');
    }
    
}
