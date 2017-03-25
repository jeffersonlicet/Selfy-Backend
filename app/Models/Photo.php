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
 * @property User; $user
 * @property Place $place
 * @property PhotoGroup $photoGroup
 */
class Photo extends Model
{
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
}
