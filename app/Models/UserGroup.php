<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $user_group_id
 * @property string $group_name
 * @property User[] $users
 */
class UserGroup extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['group_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Users()
    {
        return $this->hasMany('\App\Models\User', 'user_group', 'user_group_id');
    }
}
