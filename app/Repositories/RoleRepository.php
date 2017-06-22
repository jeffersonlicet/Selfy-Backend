<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/7/2017
 * Time: 10:52 AM
 */

namespace App\Repositories;


use App\Models\Role;

class RoleRepository
{
    protected $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function update(array $attributes, $id)
    {
        $role = $this->find($id);
        if ($attributes['display_name'] === $role->display_name) {
            unset($attributes['display_name']);
        }
        return parent::update($attributes, $id);
    }

    public function find($id)
    {
        return $this->role->findOrFail($id);
    }


}