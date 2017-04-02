<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $recognition_id
 * @property integer $face_id
 * @property string $face_external_id
 * @property string $updated_at
 * @property string $created_at
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
     * The primary key of the model.
     *
     * @var string
     */
    protected $primaryKey = 'recognition_id';

    /**
     * @var array
     */
    protected $fillable = ['face_id', 'face_external_id', 'created_at'. 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Face()
    {
        return $this->belongsTo('App\Models\UserFace', 'face_id', 'face_id');
    }
}
