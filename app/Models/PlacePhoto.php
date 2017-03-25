<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $photo_id
 * @property integer $place_id
 * @property string $photo_prefix
 * @property string $photo_suffix
 * @property string $photo_width
 * @property string $photo_height
 * @property Place $place
 */
class PlacePhoto extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['place_id', 'photo_prefix', 'photo_suffix', 'photo_width', 'photo_height'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Place()
    {
        return $this->belongsTo('App\Models\Place', 'place_id', 'place_id');
    }
}
