<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $photo_id
 * @property integer $user_id
 * @property integer $place_id
 * @property integer $photo_group
 * @property string $caption
 * @property string $url
 * @property integer $likes_count
 * @property integer $comments_count
 * @property integer $views_count
 * @property string $created_at
 * @property string $updated_at
 * @property User $User
 * @property Place $Place
 * @property PhotoGroup $Group
 */
class Photo extends Model
{
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id', 'place_id'
    ];

    protected $appends = array('like_enabled', 'delete_enabled');



    /**
     * The primary key of the model.
     *
     * @var string
     */
    protected $primaryKey = 'photo_id';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'place_id', 'photo_group', 'caption', 'url', 'likes_count', 'comments_count', 'views_count', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Place()
    {
        return $this->belongsTo('App\Models\Place', 'place_id', 'place_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Group()
    {
        return $this->belongsTo('App\Models\PhotoGroup', 'photo_group', 'photo_group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function Challenges()
    {
        return $this->hasManyThrough(
            'App\Models\Challenge',
            'App\Models\ChallengeCompleted',
            'photo_id', 'challenge_id', 'photo_id');
    }

    /**
     *  Append property
     *  Return if the user can perform a like action
     * @return bool
     */
    public function getLikeEnabledAttribute()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return !boolval(count(UserLike::where(['user_id' => \Auth::user()->user_id, 'photo_id' => $this->photo_id])->first()));
        /** @noinspection end */
    }

    /**
     *  Append property
     *  Return if the user can perform a delete action
     * @return bool
     */
    public function getDeleteEnabledAttribute()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return \Auth::user()->user_id == $this->user_id;
        /** @noinspection end */
    }

    /**
     * @param User $user
     * @param $id
     * @param $limit
     * @param $offset
     * @return Photo
     */
    public static function collection(User $user, $id, $limit, $offset)
    {
        /** @noinspection PhpUndefinedMethodInspection */
            return Photo::definition($user, $id)->with('User', 'Place', 'Challenges', 'Challenges.Object')->offset($offset)->limit($limit)->get();
        /** @noinspection end */
    }

    public static function single($id = 0)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return Photo::with('User', 'Place', 'Challenges', 'Challenges.Object')->find($id);
        /** @noinspection end */
    }

    /**
     *
     * Creates a definition to know if is a general feed or a user/profile feed
     *
     * @param $query
     * @param User $user
     * @param $requested_id
     * @return mixed
     */
    public function scopeDefinition($query, User $user, $requested_id)
    {
        switch ($requested_id)
        {
            case 0:
                /** @noinspection PhpUndefinedMethodInspection */
                $following = $user->Following->pluck('user_id');
                $following[] = $user->user_id;

                return $query->whereIn('user_id', $following);
                /** @noinspection end */
                break;
            default:
                /** @noinspection PhpUndefinedMethodInspection */
                return $query->where('user_id', $requested_id);
                /** @noinspection end */
                break;
        }
    }
}
