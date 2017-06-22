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

class RolesController extends Controller
{
    protected $repository;
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
        $a = array();
        $roles = DB::table('roles')->get();
        $permissions = DB::table('permissions')->get();
        $role_permissions = Role::all();
        foreach ($role_permissions as $role_permission) {
            foreach ($role_permission->perms as $role_permissionr) {
//                $a[] = $role_permissionr->pivot->role_id;
                $a[] = $role_permissionr;
            }
        }
        return view('admin.roles.index',
            [
                'roles' => $roles,
                'permissions' => $permissions,
                'role_permissions' => json_encode($a)
            ]
        );
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRole(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'display_name' => 'required',
                'permission_id' => 'required'
            ]);
            DB::transaction(function () use ($request) {
                $this->repository->create($request->all());
            });
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
        return view('permissions.assign')
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
        return view('roles.edit')
            ->with('role', $this->repository->find($id))
            ->with('activeMenu', 'sidebar.users.roles');
    }

    public function update($id, Request $request)
    {
        if(Auth::user()->can('update-roles')) {

            $this->validate($request, [
                'name' => 'required',
                'display_name' => 'required',
                'permission_id' => 'required'
            ]);

            $role = $this->repository->update($request->all(),$id);

            if($role->permissions->count()) {

                $role->permissions()->detach($role->permissions()->lists('permission_id')->toArray());
            }

            $role->attachPermissions($request->input('permission_id'));

            return redirect('admin/roles');
        }
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