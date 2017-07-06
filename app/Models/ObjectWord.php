<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $word_id
 * @property integer $object_wnid
 * @property string $object_words
 * @property string $created_at
 * @property string $updated_at
 */
class ObjectWord extends Model
{
    protected $primaryKey = 'word_id';
    /**
     * @var array
     */
    protected $fillable = ['object_wnid', 'object_words', 'created_at', 'updated_at'];

}
