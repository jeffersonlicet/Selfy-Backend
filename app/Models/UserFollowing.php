<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $follower_id
 * @property integer $following_id
 * @property string $created_at
 * @property string $updated_at
 * @property User $Follower
 * @property User $Following
 */
class UserFollowing extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'followers';

    protected $primaryKey = 'follow_id';

    protected $hidden = ['created_at', 'updated_at', 'follow_id', 'following_id', 'follower_id'];


    /**
     * @var array
     */
    protected $fillable = ['follower_id', 'following_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User', 'following_id', 'user_id');
    }
}
