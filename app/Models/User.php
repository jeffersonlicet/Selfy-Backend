<?php

namespace App\Models;



use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property integer user_id
 * @property integer user_group
 * @property string username
 * @property string email
 * @property string password
 * @property string firstname
 * @property string lastname
 * @property string bio
 * @property string firebase_token
 * @property string created_at
 * @property string updated_at
 * @property integer user_locale
 * @property integer spot_completed
 * @property integer duo_completed
 * @property integer play_completed
 * @property integer spot_todo
 * @property integer duo_todo
 * @property integer play_todo
 * @property string reset_password_token
 * @property string reset_password_sent_at
 * @property UserGroup userGroup
 * @property UserFace face
 * @property Photo[] photos
 * @property ChallengeCompleted[] ChallengeCompleted
 * @property ChallengeTodo[] ChallengeTodo
 */
class User extends Authenticatable
{
    use Notifiable;

    protected static $createRules = array(
        'username'              =>	'required|unique:users,username',
        'firstname'				=>	'required',
        'lastname'				=>	'required',
        'password'				=>	'required|min:6|confirmed',
        'password_confirmation'	=>	'required|min:6',
        'email'					=>	'required|email|unique:users,email',
    );

    public static function getCreateRules() { return self::$createRules; }

    /**
     * @var array
     */
    protected $fillable = ['user_group', 'username', 'email', 'password', 'firstname', 'lastname', 'bio', 'firebase_token', 'created_at', 'updated_at', 'user_locale', 'spot_completed', 'duo_completed', 'play_completed', 'spot_todo', 'duo_todo', 'play_todo', 'reset_password_token', 'reset_password_sent_at'];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'firebase_token'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Group()
    {
        return $this->belongsTo('App\Models\UserGroup', 'user_group', 'user_group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function Face()
    {
        return $this->hasOne('App\Models\Face', 'id_face', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Photos()
    {
        return $this->hasMany('App\Models\Photo', 'user_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Completed()
    {
        return $this->hasMany('App\Models\ChallengeCompleted', 'user_id', 'user_id');
    }

    /**
     * Get all of the post's comments.
     */
    public function Duos()
    {
        return $this->morphMany('App\Models\Challenges', 'object');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Todo()
    {
        return $this->hasMany('App\Models\TodoChallenge', 'user_id', 'user_id');
    }
}
