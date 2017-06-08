<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $hashtag_id
 * @property string $hashtag_text
 * @property string $created_at
 * @property string $updated_at
 * @property integer $hashtag_status
 * @property mixed $hashtag_relevance
 */
class Hashtag extends Model
{
    protected $primaryKey = "hashtag_id";

    /**
     * @var array
     */
    protected $fillable = ['hashtag_text', 'created_at', 'updated_at', 'hashtag_status'];

}
