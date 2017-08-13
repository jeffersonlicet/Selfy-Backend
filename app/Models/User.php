<?php

namespace App\Models;




use DB,
    Carbon\Carbon,
    Illuminate\Notifications\Notifiable,
    Zizaco\Entrust\Traits\EntrustUserTrait,
    Illuminate\Database\Eloquent\SoftDeletes,
    Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property boolean duo_enabled
 * @property boolean spot_enabled
 * @property integer user_id
 * @property integer user_group
 * @property string username
 * @property string email
 * @property string password
 * @property string firstname
 * @property string lastname
 * @property string bio
 * @property string avatar
 * @property string firebase_token
 * @property string created_at
 * @property string updated_at
 * @property string user_locale
 * @property integer spot_completed
 * @property integer duo_completed
 * @property integer play_completed
 * @property integer spot_todo
 * @property integer duo_todo
 * @property integer play_todo
 * @property integer followers_count
 * @property integer following_count
 * @property integer photos_count
 * @property integer account_private
 * @property integer account_verified
 * @property string reset_password_token
 * @property string reset_password_sent_at
 * @property UserGroup userGroup
 * @property UserFace $Face
 * @property UserToken token
 * @property Photo[] photos
 * @property UserFaceRecognition[] FaceDescriptors
 * @property User[] $Following
 * @property User[] $Followers
 * @property UserChallenge[] userChallenges
 * @property PhotoReport[] $PhotoReports
 * @property int gender
 * @property int facebook
 * @property int twitter
 * @property UserInformation $information
 * @property string $facebook_token
 * @property int platform
 * @property int original_platform
 */
class User extends Authenticatable
{
    use SoftDeletes  { restore as private restoreB; }
    use EntrustUserTrait { restore as private restoreA; }
    use Notifiable;

    protected $primaryKey = 'user_id';

    protected $appends = array('follow_enabled', 'edit_enabled');

    protected static $createRules = [
        'username'              =>	'required|allowed_username|unique:users,username',
        'password'				=>	'required|min:4',
    ];

    public static function getCreateRules() { return self::$createRules; }

    /**
     * @var array
     */
    protected $fillable = [
        'user_group', 'username', 'email', 'password', 'firstname', 'lastname', 'bio',
        'firebase_token', 'created_at', 'updated_at', 'user_locale', 'spot_completed', 'duo_completed',
        'play_completed', 'spot_todo', 'duo_todo', 'play_todo', 'reset_password_token', 'reset_password_sent_at',
        'duo_enabled', 'spot_enabled', 'account_private', 'save_photos', 'facebook_token', 'play_enabled',
        'original_platform'];

    /**
     * @var array
     */
    protected $hidden = [
       'deleted_at', 'password', 'reset_password_token', 'firebase_token', 'reset_password_sent_at',
        'created_at', 'updated_at', 'facebook_token'
    ];

    /**
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public static function getTopByChallenges($limit = 5)
    {
        return DB::table("user_challenges")
            ->where("user_challenges.challenge_status", "=", config('constants.CHALLENGE_STATUS.COMPLETED'))
            ->where('user_challenges.updated_at', '>=',  Carbon::now()->subDay())
            ->join('users', 'user_challenges.user_id', '=', 'users.user_id')
            ->select( 'users.user_id', 'users.username', 'users.avatar', DB::raw('count(*) as completed_count'))
            ->groupBy('users.user_id', 'users.username', 'users.avatar')
            ->limit($limit)
            ->orderBy('completed_count', 'DESC')
            ->get();
    }

    public function restore()
    {
        $this->restoreA();
        $this->restoreB();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Group()
    {
        return $this->belongsTo('App\Models\UserGroup', 'user_group', 'user_group_id');
    }

    /**
     * Bind user social information
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function information()
    {
        return $this->hasOne('App\Models\UserInformation', 'user_id', 'user_id');
    }

    /**
     * Return all
     * @return mixed
     */
    public function userChallenges()
    {

        return $this->hasMany('App\Models\UserChallenge', 'user_id', 'user_id');

        //return UserChallenge::where(['user_id' => \Auth::user()->user_id, 'challenge_status' => config('constants.CHALLENGE_STATUS.ACCEPTED')])->with('Challenge')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function Face()
    {
        return $this->hasOne('App\Models\UserFace', 'user_id', 'user_id');
    }

    public function FaceDescriptors()
    {
        return $this->hasManyThrough(
            'App\Models\UserFaceRecognition',
            'App\Models\UserFace',
            'user_id', 'face_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function Token()
    {
        return $this->hasOne('App\Models\UserToken', 'user_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Following()
    {
        return $this->hasMany('App\Models\UserFollowing', 'follower_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Followers()
    {
        return $this->hasMany('App\Models\UserFollower', 'following_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function PhotoReports()
    {
        return $this->hasMany('App\Models\PhotoReport', 'user_id', 'user_id');
    }

    /**
     *  Append property
     *  Return if the user can perform a like action
     * @return bool
     */
    public function getEditEnabledAttribute()
    {
        return !\Auth::guest() && \Auth::user()->user_id == $this->user_id ;
    }

    /**
     *  Append property
     *  Return if the user can perform a like action
     * @return bool
     */
    public function getSecondaryEmailAttribute()
    {
        if($this->facebook == config('constants.SOCIAL_STATUS.COMPLETED'))
        {
            return UserInformation::where('user_id', $this->user_id)->first()->facebook_email;
        }

        return null;
    }

    /**
     *  Append property
     *  Return if the user can perform a like action
     * @return bool
     */
    public function getFollowEnabledAttribute()
    {
        return !\Auth::guest() && \Auth::user()->user_id != $this->user_id && !boolval(count(UserInvitation::where(['user_id' => \Auth::user()->user_id, 'profile_id' => $this->user_id])->first())) &&!boolval(count(UserFollower::where(['follower_id' => \Auth::user()->user_id, 'following_id' => $this->user_id])->first()));
        /** @noinspection end */
    }

    public function followingIds($includeMe = FALSE)
    {
        $collection   = $this->Following->pluck('following_id');

        if($includeMe)
            $collection[] = $this->user_id;

        return $collection;
    }
}
