<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $user_id
 * @property integer $key_id
 * @property string $key_value
 * @property integer $key_type
 * @property integer $key_status
 * @property string $updated_at
 * @property string $created_At
 * @property User $user
 */
class UserKey extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'key_id', 'key_value', 'key_type', 'key_status', 'updated_at', 'created_At'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id', 'user_id');
    }
}
