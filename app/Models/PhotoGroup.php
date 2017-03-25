<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $photo_group_id
 * @property string $group_name
 * @property Photo[] $photos
 */
class PhotoGroup extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['group_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Photos()
    {
        return $this->hasMany('App\Models\Photo', 'photo_group', 'photo_group_id');
    }
}
