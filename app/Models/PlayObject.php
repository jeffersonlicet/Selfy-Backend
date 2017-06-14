<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $play_object_id
 * @property integer $category_id
 * @property integer $play_id
 * @property string $created_at
 * @property string $updated_at
 * @property ObjectCategory $objectCategory
 */
class PlayObject extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['category_id', 'play_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function objectCategory()
    {
        return $this->belongsTo('App\Models\ObjectCategory', 'category_id', 'category_id');
    }


    public function Play()
    {
        return $this->belongsTo('App\Models\ChallengePlay', 'play_id', 'play_id');
    }

}
