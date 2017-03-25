<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $place_id
 * @property string $place_external_id
 * @property string $name
 * @property string $category
 * @property string $category_icon_prefix
 * @property string $category_icon_suffix
 * @property string $country
 * @property string $state
 * @property string $city
 * @property float $latitude
 * @property float $longitude
 * @property string $last_update
 * @property Photo[] $photos
 * @property PlacePhoto[] $placePhotos
 */
class Place extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['place_external_id', 'name', 'category', 'category_icon_prefix', 'category_icon_suffix', 'country', 'state', 'city', 'latitude', 'longitude', 'last_update'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Here()
    {
        return $this->hasMany('App\Models\Photo', 'place_id', 'place_id');
    }

    /**
     * Get all of the post's comments.
     */
    public function Spots()
    {
        return $this->morphMany('App\Models\Challenges', 'object');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Photos()
    {
        return $this->hasMany('App\Models\PlacePhoto', 'place_id', 'place_id');
    }
}
