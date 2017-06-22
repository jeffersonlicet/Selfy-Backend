<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/5/2017
 * Time: 10:32 PM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\AclManager;

class UsersController extends Controller
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index(Request $request)
    {
        $user = $this->repository->all(['id', 'name', 'email']);

        return view('user.index', compact('user'))->with('activeMenu', 'sidebar.users.list');
    }

    public function edit(AclManager $aclManager, $id)
    {
        $user = $this->repository->find($id);
        return view('user.edit')
            ->with('user', $user)
            ->with('roles', $aclManager->getRolesForSelect())
            ->with('activeMenu', 'sidebar.users');
    }
}