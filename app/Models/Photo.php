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
 * @property integer $reports_count
 * @property string $created_at
 * @property string $updated_at
 * @property User $User
 * @property Place $Place
 * @property PhotoGroup $Group
 * @property UserLike[] $UserLikes
 * @property int adult_content
 */
class Photo extends Model
{
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id', 'place_id', 'reports_count'
    ];

    protected $appends = array('like_enabled', 'delete_enabled', 'report_enabled');


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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Challenges()
    {
        return $this->belongsToMany(
            'App\Models\Challenge',
                'user_challenges',
                'photo_id',
                'challenge_id',
                'photo_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Hashtags()
    {
        return $this->belongsToMany(
            'App\Models\Hashtag',
            'photo_hashtags',
            'photo_id',
            'hashtag_id',
            'photo_id');
    }

    /**
     *  Append property
     *  Return if the user can perform a like action
     * @return bool
     */
    public function getLikeEnabledAttribute()
    {

        return !\Auth::guest() && !boolval(count(UserLike::where(['user_id' => \Auth::user()->user_id, 'photo_id' => $this->photo_id])->first()));
        /** @noinspection end */
    }

    /**
     *  Append property
     *  Return if the user can perform a delete action
     * @return bool
     */
    public function getDeleteEnabledAttribute()
    {

        return !\Auth::guest() && \Auth::user()->user_id == $this->user_id;
        /** @noinspection end */
    }

    /**
     *  Append property
     *  Return if the user can perform a report action
     * @return bool
     */
    public function getReportEnabledAttribute()
    {

        return !\Auth::guest() && !boolval(count(PhotoReport::where(['user_id' => \Auth::user()->user_id, 'photo_id' => $this->photo_id])->first()));
        /** @noinspection end */
    }

    /**
     * Return a collection of a personalized feed
     * removing reported and adult content photos
     *
     * @param User $user
     * @param $user_id
     * @param $limit
     * @param $offset
     * @return Photo
     * @internal param $id
     */
    public static function collection(User $user, $user_id, $limit, $offset)
    {
        return Photo::feedDefinition($user, $user_id)->filterReportsDefinition()->where(['adult_content' => 0])->with('User', 'Place', 'Challenges', 'Challenges.Object')->offset($offset)->limit($limit)->orderBy('photo_id', 'desc')->get();
    }

    /**
     *
     * Get recent photos collection except reported
     * by user and adult content
     *
     * @param User $user
     * @param $limit
     * @param $offset
     * @return mixed
     */
    public static function recent(User $user, $limit, $offset)
    {

        return Photo::recentDefinition($user)->filterReportsDefinition()->where(['adult_content' => 0])->whereHas('User', function ($query) {
            $query->where('account_private', '=',  0);
        })->with('User')->offset($offset)->limit($limit)->orderBy('photo_id', 'desc')->get();

    }

    /**
     * Return a single photo
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public static function single($id)
    {

        return Photo::with('User', 'Place', 'Challenges', 'Challenges.Object')->find($id);
        /** @noinspection end */
    }

    /**
     * Return a collection of photos related by date
     *
     * @param $photo_id
     * @param $user_id
     * @param $relation
     * @param $limit
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function related($photo_id, $user_id, $relation, $limit)
    {

        return Photo::with('User', 'Place', 'Challenges', 'Challenges.Object')->where(['user_id'=> $user_id, ['photo_id', $relation, $photo_id]])->limit($limit)->orderBy('photo_id', 'desc')->get();
        /** @noinspection end */
    }

    /**
     *
     * Creates a definition to know if is a general feed
     * or a user/profile feed
     *
     * @param $query
     * @param User $user
     * @return mixed
     */
    public function scopeRecentDefinition($query, User $user)
    {
       $following = $user->Following->pluck('following_id');
       $following[] = $user->user_id;

       return $query->whereNotIn('user_id', $following);

    }

    /**
     * Creates a definition to know if is a general feed
     * or a user/profile feed
     *
     * @param $query
     * @param User $user
     * @param $requested_id
     * @return mixed
     */
    public function scopeFeedDefinition($query, User $user, $requested_id)
    {
        switch ($requested_id)
        {
            case 0:

                $following = $user->Following->pluck('following_id');
                $following[] = $user->user_id;

                return $query->whereIn('user_id', $following);
                /** @noinspection end */
                break;
            default:

                return $query->where('user_id', $requested_id);
                /** @noinspection end */
                break;
        }
    }

    /**
     * Creates a definition to filter reported selfies
     * from the current user
     *
     * @param $query
     * @return mixed
     */
    public function scopeFilterReportsDefinition($query)
    {
        return $query->whereNotIn('photo_id', \Auth::user()->PhotoReports->pluck('photo_id'));
    }
}
