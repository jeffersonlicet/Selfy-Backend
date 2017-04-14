<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $challenge_id
 * @property integer $user_id
 * @property Challenge $Challenge
 * @property User $User
 * @property  string created_at
 */
class ChallengeTodo extends Model
{
    protected $table = 'todo_challenges';
    protected $primaryKey = 'todo_id';
    public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = ['challenge_id', 'user_id'];



    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Challenge()
    {
        return $this->belongsTo('App\Models\Challenge', 'challenge_id', 'challenge_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User', 'challenge_id', 'user_id');
    }
}
