<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $created_at
 * @property string $updated_at
 * @property ObjectCategory $parent
 * @property int $category_id
 * @property  string $category_wnid
 * @property string $parent_wnid
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
    protected $fillable = ['parent_wnid', 'created_at', 'updated_at', 'category_wnid'];

    public function Word()
    {
        return $this->hasOne(ObjectWord::class, 'object_wnid', 'category_wnid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Parent()
    {
        return $this->belongsTo(ObjectCategory::class, 'parent_wnid', 'category_wnid');
    }

}
