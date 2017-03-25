<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $challenge_id
 * @property integer $user_id
 * @property Challenge $challenge
 * @property User $user
 */
class ChallengeTodo extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['challenge_id', 'user_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function challenge()
    {
        return $this->belongsTo('App\Models\Challenge', 'challenge_id', 'challenge_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'challenge_id', 'user_id');
    }
}
