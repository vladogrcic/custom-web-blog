<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Storage;
class User extends Authenticatable
{
    use Notifiable;
    use LaratrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public $inFolderLocs = [
        'orig'=>'avatars',
        'thumbs'=>[
            'avatars_thumb-640', 'avatars_thumb-360', 'avatars_thumb-50'
        ]
    ];
    public function post($value='')
    {
        return $this->hasMany('App\Post');
    }
    public function profile($value='')
    {
        return $this->hasMany('App\Profile');
    }
    /**
     * Gets user Gravatar url.
     *
     * @param string|bool $email Email of the Gravatar which should be retrieved.
     * @param int $size Size of image.
     * @return string Url of Gravatar image.
     * 
     */
    public function getGravatar($email = false, $size=50)
    {
        if($email) $email2 = $email;
        else $email2 = $this->attributes['email'];
        $hash = md5(strtolower(trim($email2)));
        return "http://www.gravatar.com/avatar/$hash?s=".$size;
    }
    public function getAvatar($email = false, $id = false, $size=50)
    {
        $checkIfGravatarExists = $this->checkGravatar($email);
        $configDisks = config('filesystems.disks.'.$this->inFolderLocs['orig']);
        $default = url('/').'/images/user_anonymous.svg';
        $user_id = $id;
        $checkGravatar = Profile::where('user_id', '=', $user_id)->where('meta_key', '=', 'gravatar')->first();
        
        if($checkGravatar){
            $checkGravatar = Profile::find($checkGravatar->id)->meta_value;
        }
        if (!$checkGravatar) {
            $checkAvatar = Profile::where('user_id', '=', $user_id)->where('meta_key', '=', 'avatar')->first();
            if($checkAvatar){
                $avatar = Profile::find($checkAvatar->id)->meta_value;
                if ($avatar) {
                    return Storage::url('avatars/thumb-50/'.$avatar);
                }
            }
            else{
                return $default;
            }
        } else {
            if($checkIfGravatarExists){
                return $this->getGravatar($email, $size);
            }
            else{
                return $default;
            }
        }
    }
    /**
     * Checks if image on gravatar exists.
     *
     * @param string|bool $email Email of the Gravatar which should be retrieved.
     * @return bool Does the avatar exist?
     * 
     */
    public function checkGravatar($email) {
        $hash = md5($email);
        $uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
        $headers = @get_headers($uri);
        if (!preg_match("|200|", $headers[0])) {
            $has_valid_avatar = FALSE;
        } else {
            $has_valid_avatar = TRUE;
        }
        return $has_valid_avatar;
    }
}
