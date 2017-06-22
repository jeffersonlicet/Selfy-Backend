<?php

namespace App\Models;


use Zizaco\Entrust\EntrustPermission as Model;

/**
 * Class Permission
 * @package App\Models
 */
class Permission extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['display_name', 'name', 'description'];
}


