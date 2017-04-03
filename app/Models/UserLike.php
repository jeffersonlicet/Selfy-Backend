<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $user_id
 * @property integer $photo_id
 * @property string $created_at
 * @property User $User
 * @property Photo $Photo
 */
class UserLike extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'likes';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'photo_id', 'created_at'];

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
    public function Photo()
    {
        return $this->belongsTo('App\Models\Photo', 'photo_id', 'photo_id');
    }
}
