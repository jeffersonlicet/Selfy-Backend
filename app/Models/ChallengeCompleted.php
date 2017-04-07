<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $photo_id
 * @property integer $challenge_id
 * @property integer $user_id
 * @property Photo $photo
 * @property Challenge $challenge
 * @property User $user
 * @property string created_at
 */
class ChallengeCompleted extends Model
{
    protected $table = 'completed_challenges';
    protected $primaryKey = 'challenge_id';
    public $timestamps = false;
    protected $dateFormat = 'M j Y h:i:s:000A';
    /**
     * The primary key of the model.
     *
     * @var string
     */

    /**
     * @var array
     */
    protected $fillable = ['photo_id', 'challenge_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Photo()
    {
        return $this->belongsTo('App\Models\Photo', 'photo_id', 'photo_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Challenge()
    {
        return $this->belongsTo('App\Models\Challenge', 'challenge_id', 'challenge_id');
    }
}
