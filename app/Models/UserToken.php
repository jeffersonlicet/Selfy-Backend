<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $token_id
 * @property integer $user_id
 * @property string $public_key
 * @property string $device_id
 * @property string $device_os
 * @property string $private_key
 * @property User $user
 */
class UserToken extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tokens';

    /**
     * The primary key of the model.
     *
     * @var string
     */
    protected $primaryKey = 'token_id';

    /**
     * Inject timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'public_key', 'device_id', 'device_os', 'private_key'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }
}
