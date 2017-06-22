<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/14/2017
 * Time: 10:11 AM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = \DB::table('permissions')->get();
        return response()->json($permissions);
        //return view('vista.permissions', ['permissions' => $permissions]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createPermission(Request $request)
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
    public function editPermission(Request $request, $id)
    {
        $name = $request->input('name');
        $display_name = $request->input('display_name');
        $description = $request->input('description');
        \DB::table('permissions')
            ->where('id', $id)
            ->update(['name' => $name, 'display_name' => $display_name, 'description' => $description]);
        return back()->withInput();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removePermission($id)
    {
        \DB::table('permissions')->where('id', '=', $id)->delete();
        return back()->withInput();
    }
}