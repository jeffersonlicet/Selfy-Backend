<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $challenge_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $completed_count
 * @property ChallengeCompleted[] $completedChallenges
 * @property User[] $users
 * @property string $object_type
 * @property int $object_id
 */
class Challenge extends Model
{
    /**
     * The primary key of the model.
     *
     * @var string
     */
    protected $primaryKey = 'challenge_id';
    /**
     * @var array
     */
    protected $fillable = ['object_type', 'created_at', 'updated_at', 'completed_count', 'object_id'];

    public function Object()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    /*public function Object()
    {
        switch ($this->challenge_type)
        {
            case Config::get('constants.CHALLENGE_TYPES.DUO'):
                return $this->belongsTo('App\Models\User', 'object_id', 'user_id');

            case Config::get('constants.CHALLENGE_TYPES.SPOT'):
                return $this->belongsTo('App\Models\Place', 'object_id', 'place_id');

            case Config::get('constants.CHALLENGE_TYPES.PLAY'):
                return $this->belongsTo('App\Models\User', 'object_id', 'play_id');

            default:
                return null;
        }
    }*/

    /**
 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
 */
    public function TodoBy()
    {
        return $this->belongsToMany('App\Models\User', 'todo_challenges');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function CompletedBy()
    {
        return $this->belongsToMany('App\Models\User', 'completed_challenges');
    }
}
