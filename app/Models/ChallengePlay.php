<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $play_id
 * @property string $play_title
 * @property string $play_description
 * @property string $play_sample
 * @property Hashtag $hashtag_relation
 */
class ChallengePlay extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'play';

    protected $primaryKey = 'play_id';

    /**
     * @var array
     */
    protected $fillable = ['play_title', 'play_description', 'play_sample'];

    protected $hidden = ['hashtag_relation'];

    /**
     * @var array
     */
    protected $appends = ['hashtag'];

    public function hashtag_relation()
    {
        return $this->hasOne('App\Models\Hashtag', 'hashtag_id', 'play_hashtag');
    }

    public function getHashtagAttribute()
    {
        return $this->hashtag_relation;
    }
}
