<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $comment_id
 * @property integer $user_id
 * @property string $body
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property integer photo_id
 */
class UserComment extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'comments';

    protected $primaryKey = 'comment_id';

    protected $hidden = ['updated_at', 'photo_id'];
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'body', 'created_at', 'updated_at', 'photo_id'];

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
