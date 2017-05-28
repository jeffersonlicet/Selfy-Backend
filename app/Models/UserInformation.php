<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $information_id
 * @property integer $user_id
 * @property float $facebook_id
 * @property float $twitter_id
 * @property string $created_at
 * @property string $updated_at
 * @property User $User
 */
class UserInformation extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'user_information';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'facebook_id', 'twitter_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }
}
