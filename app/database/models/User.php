<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Cartalyst\Sentry\Users\Eloquent\User as SentryUser;


class User extends SentryUser implements UserInterface, RemindableInterface
{

    protected $guarded = ['cid', 'email', 'rating', 'is_webmaster', 'is_staff'];
    protected $primaryKey = 'cid';
    protected $positions;
    protected static $hasher;
    public function __construct()
    {
        static::$hasher = new Cartalyst\Sentry\Hashing\NativeHasher();
    }

    //scopes

    public function scopeMentors($query)
    {
        return $query->where('is_mentor', '=', 1);
    }

    public function scopeInstructors($query)
    {
        return $query->where('is_instructor', '=', 1);
    }

    public function scopeStaff($query)
    {
        return $query->where('is_atm', '=', 1)
                     ->orWhere('is_datm', '=', 1)
                     ->orWhere('is_ta', '=', 1)
                     ->orWhere('is_mentor', '=', 1)
                     ->orWhere('is_instructor', '=', 1)
                     ->orWhere('is_facilities', '=', 1)
                     ->orWhere('is_webmaster', '=', 1);
    }

    public function scopeExecutive($query)
    {
        return $query->where('is_atm', '=', 1)
                     ->orWhere('is_datm', '=', 1)
                     ->orWhere('is_ta', '=', 1);
    }

    //relations
    public function settings()
    {
        return $this->hasOne('UserSettings', 'cid', 'cid');
    }

    public function group()
    {
        return $this->hasMany('ControllerGroup');
    }

    public function training()
    {
        return $this->hasMany('TrainingSession', 'cid', 'cid');
    }

    public function exams()
    {
        return $this->hasMany('Exam', 'cid', 'cid');
    }

    public function certification()
    {
        return $this->hasOne('CertType', 'id', 'cert');
    }

    public function subscriptions()
    {
        return $this->hasMany('Subscription', 'cid', 'cid');
    }

    public function rating()
    {
        return $this->hasOne('Rating', 'id', 'rating_id');
    }


    //auth related -- mostly deprecated sine we switched to Sentry
    public function is($group)
    {
        return $this->inGroup(\Sentry::findGroupByName($group));
    }

    public function has($permission)
    {
        $has = false;
        foreach($this->groups as $group) {
            $perms = $group->getPermissions();
            if(isset($perms[$permission]) && $perms[$permission] == 1) {
                $has = true;
            }
        }
        return $has;
    }


    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    protected $hidden = array('password');

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public function avatar($s = 100, $d = 'mm', $r = 'r', $img = false, $atts = array() ) {
        if(empty($this->settings->avatar)) {
            $url = 'http://www.gravatar.com/avatar/';
            $url .= md5(strtolower(trim($this->email)));
            $url .= "?s=$s&d=$d&r=$r";
            if ($img) {
                $url = '<img src="' . $url . '"';
                foreach ($atts as $key => $val)
                    $url .= ' ' . $key . '="' . $val . '"';
                $url .= ' />';
            }
            return $url;
        }
        else return $this->settings->avatar;
    }

    public function is_exec()
    {
        $exec = \Sentry::findGroupByName('Executive Staff');
        return $this->inGroup($exec);
    }

    public function is_instructor()
    {
        $ins = \Sentry::findGroupByName('Instructors');
        return $this->inGroup($ins);
    }

    public function is_staff()
    {
        $staff = \Sentry::findGroupByName('Staff');
        return $this->inGroup($staff);
    }

    public function is_mentor()
    {
        $mtr = \Sentry::findGroupByName('Mentors');
        return $this->inGroup($mtr);
    }
}
