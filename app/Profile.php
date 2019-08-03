<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public function user($value='')
    {
        return $this->belongsTo('App\User', 'author_id');
    }
}
