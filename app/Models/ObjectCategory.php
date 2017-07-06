<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $category_parent
 * @property string $created_at
 * @property string $updated_at
 * @property ObjectCategory $parent
 * @property int $category_id
 * @property  string $category_wnid
 */
class ObjectCategory extends Model
{
    protected $primaryKey = 'category_id';
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'object_categories';

    /**
     * @var array
     */
    protected $fillable = ['category_parent', 'created_at', 'updated_at', 'category_wnid'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Parent()
    {
        return $this->belongsTo('App\Models\ObjectCategory', 'category_parent', 'category_id');
    }

}
