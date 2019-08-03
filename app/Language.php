<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public function Posts(){
        return $this->hasMany('App\Post');
    }
    public function Tags(){
        return $this->hasMany('App\Tag');
    }
    public function Categories(){
        return $this->hasMany('App\Category');
    }
}
