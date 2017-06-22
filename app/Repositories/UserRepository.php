<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/5/2017
 * Time: 10:33 PM
 */

namespace App\Repositories;


use App\Models\User;

class UserRepository
{

    public function updateRoles($user, $data)
    {
        $user->roles()->sync($data['roles']);
    }
}