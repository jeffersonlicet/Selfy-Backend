<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 5/31/2017
 * Time: 7:48 PM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Challenge;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.pages.dashboard')->with(['pageTitle' => 'Selfy administration']);
    }

    public function play()
    {
        $challenges = Challenge::where('object_type', config('constants.CHALLENGE_TYPES_STR.PLAY'))
            ->paginate(15);

        return view('admin.pages.play')->with(['pageTitle' => 'Play challenges',
            'challenges' => $challenges]);
    }
}