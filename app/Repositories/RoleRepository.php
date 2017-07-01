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
    protected $model;

    protected $page;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function get()
    {
        return $this->model->get();
    }
    //Falta crear Roles.
    public function create(array $attributes = [])
    {
        $model = $this->model->create($attributes);

        return $model;
    }

    public function update(array $attributes, $id)
    {
        $model = $this->model->findOrFail($id);
        $model->fill($attributes);
        $model->save();
        return $model->fresh('perms');
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function delete($id)
    {
        is_int($id) ? $model = $this->model->findOrFail($id) : $this->model->findOrFail($id);
        $model->delete();
        return true;
    }
}