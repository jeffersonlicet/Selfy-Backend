<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $user_challenge_id
 * @property integer $challenge_id
 * @property integer $user_id
 * @property integer $challenge_status
 * @property integer $photo_id
 * @property string $updated_at
 * @property string $created_at
 * @property Challenge $challenge
 * @property User $user
 */
class UserChallenge extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['challenge_id', 'user_id', 'challenge_status', 'photo_id', 'updated_at', 'created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Challenge()
    {
        return $this->belongsTo('App\Challenge', null, 'challenge_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\User', null, 'user_id');
    }
}
