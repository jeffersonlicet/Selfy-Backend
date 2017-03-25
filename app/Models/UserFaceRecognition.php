<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $recognition_id
 * @property integer $face_id
 * @property string $face_external_id
 * @property string $date_recognition
 * @property UserFace $face
 */
class UserFaceRecognition extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'face_recognitions';

    /**
     * @var array
     */
    protected $fillable = ['face_id', 'face_external_id', 'date_recognition'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Face()
    {
        return $this->belongsTo('App\Models\UserFace', 'face_id', 'face_id');
    }
}
