<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $play_id
 * @property string $title
 * @property string $description
 * @property string $sample
 */
class ChallengePlay extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'play';

    /**
     * @var array
     */
    protected $fillable = ['title', 'description', 'sample'];
}
