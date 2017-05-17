<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $report_id
 * @property integer $photo_id
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 */
class PhotoReport extends Model
{
    protected $primaryKey = 'report_id';
    /**
     * @var array
     */
    protected $fillable = ['photo_id', 'user_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }
}
