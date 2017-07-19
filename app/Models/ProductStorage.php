<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $title
 * @property string $meli_id
 * @property integer $price
 * @property string $thumbnail
 * @property string $permalink
 * @property integer $status
 * @property int target_id
 */
class ProductStorage extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['created_at', 'updated_at', 'title', 'meli_id', 'price', 'thumbnail', 'permalink', 'status', 'target_id'];

}
