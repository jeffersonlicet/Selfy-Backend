<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $face_id
 * @property integer $user_id
 * @property string $url
 * @property User $user
 */
class UserFace extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'faces';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'url'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }
}
