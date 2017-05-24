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
 * @property User $User
 */
class UserKey extends Model
{

    protected $primaryKey = 'key_id';
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'key_id', 'key_value', 'key_type', 'key_status', 'updated_at', 'created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }
}
