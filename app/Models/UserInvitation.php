<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $profile_id
 * @property integer $invitation_id
 * @property integer $user_id
 * @property integer $invitation_status
 * @property string $created_at
 * @property string $updated_at
 * @property User $Creator
 * @property User $Profile
 */
class UserInvitation extends Model
{

    protected $primaryKey = 'invitation_id';

    /**
     * @var array
     */
    protected $fillable = ['profile_id', 'invitation_id', 'user_id', 'invitation_status', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Creator()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Profile()
    {
        return $this->belongsTo('App\Models\User', 'profile_id', 'user_id');
    }
}
