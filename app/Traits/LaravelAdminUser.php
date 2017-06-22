<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/1/2017
 * Time: 6:07 PM
 */

namespace App\Traits;

use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * Trait LaravelAdminUser
 * @package app\Traits
 */
trait LaravelAdminUser
{
    use EntrustUserTrait;
    /**
     * @return array
     */
    public function getRolesForSelect()
    {
        $data = [];
        $this->roles->each(function ($role) use (&$data) {
            $data[] = $role->id;
        });
        return $data;
    }
}