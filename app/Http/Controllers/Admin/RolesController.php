<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/7/2017
 * Time: 10:53 AM
 */

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Repositories\RoleRepository;
use App\Validation\ValidatorException;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class RolesController
 * @package App\Http\Controllers\Admin
 */
class RolesController extends Controller
{
    /**
     * @var RoleRepository
     */
    protected $repository;

    /**
     * RolesController constructor.
     * @param RoleRepository $repository
     */
    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role_permissions = array();
        $roles = $this->repository->get();
        $permissions = DB::table('permissions')->get();
        $role_permission = Role::all();
        foreach ($role_permission as $role_perm) {
            foreach ($role_perm->perms as $role_permissionr) {
//                $a[] = $role_permissionr->pivot->role_id;
                $role_permissions[] = $role_permissionr;
            }
        }
        return view('admin.roles.index',
            compact('roles', 'permissions', 'role_permissions')

        );
    }


    /**
     * @return $this
     */
    public function create()
    {
        return view('admin.roles.create')->with('activeMenu', 'sidebar.users.roles');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|unique:roles',
                'display_name' => 'required',
                'description' => 'required'
            ]);
            $this->repository->create($request->all());
        } catch (ValidatorException $e) {
            return redirect()->back()->withErrors($e->getMessageBag());
        }
        return redirect()->to(config('selfy-admin.routePrefix', 'admin') . '/roles');
    }
    /**
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rolePermissions(Request $request, $id)
    {
        $role = $this->repository->find($id);
        $permissions = Permission::all()->pluck('display_name', 'id')->toArray();
        return view('admin.permissions.assign')
            ->with('type', 'role')
            ->with('model', $role)
            ->with('permissions', $role->perms->pluck('id')->toArray())
            ->with('permissionsList', $permissions)
            ->with('activeMenu', 'sidebar.users.roles');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.roles.edit')
            ->with('role', $this->repository->find($id));
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'display_name' => 'required',
            'description' => 'required'
        ]);
        $this->repository->update($request->all(),$id);
        return redirect('admin/roles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeRole($id, $permission)
    {
        if(Auth::user()->can('delete-roles')) {
            $role = $this->repository->find($id);
            $role->perms()->detach($permission);

            return redirect('admin/roles');
        }
        return redirect('auth/logout');
    }
}