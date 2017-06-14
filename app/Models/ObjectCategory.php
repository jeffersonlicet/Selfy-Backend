<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $category_id
 * @property integer $category_parent
 * @property string $created_at
 * @property string $updated_at
 * @property string $category_name
 * @property ObjectCategory $parent
 */
class ObjectCategory extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'object_categories';

    /**
     * @var array
     */
    protected $fillable = ['category_parent', 'created_at', 'updated_at', 'category_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\ObjectCategory', 'category_parent', 'category_id');
    }

}