<?php

namespace App\Models;

use Gibbo\Foursquare\Client\Entity\Venue\Venue;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $place_id
 * @property string $place_external_id
 * @property string $name
 * @property string $category
 * @property string $category_icon
 * @property string $country
 * @property string $state
 * @property string $city
 * @property float $latitude
 * @property float $longitude
 * @property string $last_update
 * @property Photo[] $photos
 * @property PlacePhoto[] $placePhotos
 * @property string address
 */
class Place extends Model
{
    /**
     * The primary key of the model.
     *
     * @var string
     */
    protected $primaryKey = 'place_id';

    /**
     * @var array
     */
    protected $hidden = [
        'place_external_id', 'created_at', 'updated_at', 'category_icon_suffix'
    ];

    /**
     * @var array
     */
    protected $fillable = ['place_external_id', 'name', 'category', 'category_icon_prefix', 'category_icon_suffix', 'country', 'state', 'city', 'latitude', 'longitude', 'updated_at', 'created_at'];

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

    public function fillFromVenue(Venue $venue, $coordinates)
    {
        $this->place_external_id   = $venue->getIdentifier();
        $this->name                = $venue->getName();
        $this->category            = $venue->getPrimaryCategory() == null ? '' :  $venue->getPrimaryCategory()->getName();
        $this->category_icon       =  $venue->getPrimaryCategory() == null ? '' : $venue->getPrimaryCategory()->getIconUrl();
        $this->country             = $venue->getLocation()->getCountry();
        $this->state               = $venue->getLocation()->getState();
        $this->city                = $venue->getLocation()->getCity();
        $this->address             = $venue->getLocation()->getAddress();
        $this->latitude            = $coordinates[0];
        $this->longitude           = $coordinates[1];
    }

}
