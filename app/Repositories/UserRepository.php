<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/5/2017
 * Time: 10:33 PM
 */

namespace App\Repositories;


use App\Models\User;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository
{

    /**
     * @var User
     */
    protected $user;

    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param $user
     * @param $data
     */
    public function updateRoles($user, $data)
    {
        $this->user->findOrFail($user->id);
        $user->roles()->sync($data['roles']);
    }

}