<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/1/2017
 * Time: 6:06 PM
 */

namespace App\Models;

use Zizaco\Entrust\EntrustRole as Model;
class Role  extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['display_name', 'name', 'description'];
}