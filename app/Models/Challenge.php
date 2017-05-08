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
 * @property User|Place Object
 * @property int $challenge_status
 */
class Challenge extends Model
{
    /**
     * The primary key of the model.
     *
     * @var string
     */
    protected $primaryKey = 'challenge_id';

    protected $hidden = [
        'object_id', 'photo_id'
    ];
    /**
     * @var array
     */
    protected $fillable = ['object_type', 'created_at', 'updated_at', 'completed_count', 'object_id'];

    protected $appends = ['challenge_status'];

    public function Object()
    {
        return $this->morphTo();
    }
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

    /**
     * @return int
     */
    public function getChallengeStatusAttribute()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if(!\Auth::guest() && $invitation = UserChallenge::where(['user_id' => \Auth::user()->user_id, 'challenge_id' => $this->challenge_id])->first())
        {
            return $invitation->challenge_status;
        }

        return -1;
    }
}
