<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $photo_mention_id
 * @property integer $user_id
 * @property integer $photo_id
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property Photo $photo
 */
class UserPhotoMention extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'photo_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function photo()
    {
        return $this->belongsTo('App\Photo', 'photo_id', 'photo_id');
    }
}
