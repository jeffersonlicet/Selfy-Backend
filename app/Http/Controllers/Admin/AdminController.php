<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 5/31/2017
 * Time: 7:48 PM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
}