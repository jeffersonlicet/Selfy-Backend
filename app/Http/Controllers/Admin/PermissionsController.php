<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/14/2017
 * Time: 10:11 AM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * Class PermissionsController
 * @package App\Http\Controllers\Admin
 */
class PermissionsController extends Controller
{
    /**
     * @var Permission
     */
    protected $permission;

    /**
     * @var Role
     */
    protected $role;

    /**
     * PermissionsController constructor.
     * @param Permission $permission
     * @param Role $role
     */
    public function __construct(Permission $permission, Role $role)
    {
        $this->permission = $permission;
        $this->role = $role;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = $this->permission->get();
        return response()->json($permissions);
        //return view('vista.permissions', ['permissions' => $permissions]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        return view('admin.permissions.create');
    }



    /**
     * @param Request $request
     * @return $this
     */
    public function store(Request $request)
    {
        $createPost = new Permission();
        $createPost->name         = $request->input('name');
        $createPost->display_name = $request->input('display_name'); // optional
        // Allow a user to...
        $createPost->description  = $request->input('description'); // optional
        $createPost->save();
        return back()->withInput();
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.permissions.edit')
            ->with('permissions', $this->permission->find($id));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $permission = $this->permission->find($id);
        //dd($request->all());
        $permission->name = $request->get('name');
        $permission->display_name = $request->get('display_name');
        $permission->description = $request->get('description');
        $permission->save();

        return redirect()->to(config('selfy-admin.routePrefix', 'admin') . '/permissions');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $this->permission->where('id', '=', $id)->delete();
        return back()->withInput();
    }
}