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

        return view('users.index', compact('user'))->with('activeMenu', 'sidebar.users.list');
    }
}