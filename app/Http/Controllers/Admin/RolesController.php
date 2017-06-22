<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/7/2017
 * Time: 10:53 AM
 */

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RolesController extends Controller
{
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
        return view('functions.roles',
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
        $name               = $request->input('rolename');
        $description        = $request->input('description');
        $role               = new Role();
        $role->name         = $name;
        $role->display_name = 'Project '.$name; // optional
        $role->description  = $description; // optional
        $role->save();
        return back()->withInput();
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
        $permissions = $request->get('already_values');
        $permissionId = array();
        DB::table('permission_role')->where('role_id', '=', $id)->delete();
        if ($permissions){
            $temp = DB::table('permissions')
                ->whereIn('id', $permissions)
                ->get();
            foreach ($temp as $tmp) {
                $permissionId[] = $tmp->id;
            }
            $role = Role::where('id', '=', $id)->first();
            $role->attachPermissions($permissionId);
        }
        return back()->withInput();
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
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editRole(Request $request, $id)
    {
        $name = $request->input('rolename');
        $description = $request->input('description');
        DB::table('roles')
            ->where('id', $id)
            ->update(['name' => $name, 'description' => $description]);
        return back()->withInput();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeRole($id)
    {
        DB::table('roles')->where('id', '=', $id)->delete();
        return back()->withInput();
    }
}