<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $photo_hashtag_id
 * @property integer $photo_id
 * @property integer $hashtag_id
 * @property string $created_at
 * @property string $updated_at
 * @property Photo $photo
 * @property Hashtag $hashtag
 */
class PhotoHashtag extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['photo_id', 'hashtag_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function photo()
    {
        return $this->belongsTo('App\Model\Photo', 'photo_id', 'photo_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hashtag()
    {
        return $this->belongsTo('App\Model\Hashtag', 'hashtag_id', 'hashtag_id');
    }
}
